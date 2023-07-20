<?php

use App\Models\GhlAuth;
use App\Models\Menu;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Else_;

function uploadFile($file, $path, $name)
{
    $name = $name . '.' . $file->ClientExtension();
    $file->move($path, $name);
    return $path . '/' . $name;
}

function setting($key, $default = '')
{
    $setting = DB::table('settings')->where(['user_id' => login_id(), 'key' => $key])->first();
    if ($setting) {
        return $setting->value;
    }
    return $default;
}

function save_settings($key, $value = '')
{
    $user_id = login_id();
    $setting = Setting::updateOrCreate(
        ['user_id' => $user_id, 'key' => $key],
        [
            'value' => $value,
            'user_id' => $user_id,
            'key' => $key,
        ]
    );
    return $setting;
}

function save_auth($code, $type = 'code')
{
    $user_id = login_id();
    $data = [
        'access_token' => $code->access_token,
        'refresh_token' => $code->refresh_token,
        'user_id' => $user_id,
    ];

    if (empty($type)) {
        $data['location_id'] = $code->locationId ?? $user_id;
        $data['user_type'] = $code->userType ?? 'Location';
    }

    $auth = GhlAuth::updateOrCreate(
        ['user_id' => $user_id],
        $data
    );
    return $auth;
}

function login_id($id = "")
{
    if (!empty($id)) {
        return $id;
    }
    $id = auth()->user()->id;
    return $id;
}

function is_role()
{
    if (auth()->user()->role == 0) {
        return 'admin';
    } elseif (auth()->user()->role == 1) {
        return 'company';
    } else {
        return 'user';
    }
}

function get_fields($vars)
{
    $vars = $vars['__data'];
    unset($vars['__env']);
    unset($vars['app']);
    unset($vars['errors']);
    return $vars;
}

function is_connected()
{
    $user_id = login_id();
    $user = Setting::where('user_id', $user_id)->first();
    if ($user) {
        return true;
    }
    return false;
}

if (!function_exists('ghl_oauth_call')) {

    function ghl_oauth_call($code = '', $method = '')
    {
        $url = 'https://api.msgsndr.com/oauth/token';
        $curl = curl_init();
        $data = [];
        $data['client_id'] = setting('client_id');
        $data['client_secret'] = setting('client_secret');
        $md = empty($method) ? 'code' : 'refresh_token';
        $data[$md] = $code;
        $data['grant_type'] = empty($method) ? 'authorization_code' : 'refresh_token';
        $postv = '';
        $x = 0;
        foreach ($data as $key => $value) {
            if ($x > 0) {
                $postv .= '&';
            }
            $postv .= $key . '=' . $value;
            $x++;
        }
        $curlfields = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postv,
        );
        curl_setopt_array($curl, $curlfields);
        $response = curl_exec($curl);
        $response = json_decode($response);
        curl_close($curl);
        return $response;
    }
}

function ghl_token($request, $type = '')
{
    $code = $request->code;
    $code = ghl_oauth_call($code, $type);
    $route = '/';
    $id = login_id();
    if ($code) {
        if (property_exists($code, 'access_token')) {
            session()->put('ghl_api_token', $code->access_token);
            save_auth($code, $type);
            abort(redirect()->route('dashboard')->with('success', 'Connected successfully'));
        } else {
            if (property_exists($code, 'error_description')) {
                if (empty($type)) {
                    abort(redirect()->route('dashboard')->with('error', $code->error_description));
                }
            }
            return null;
        }
    } else {
        abort(redirect()->route('dashboard')->with('error', 'Server error'));
    }
}

function get_table_data($table, $query = '')
{
    $data = DB::table($table)->$query;
    return $data;
}

function getActions($actions = [], $route = '')
{
    //to camel case
    $acs = [];
    foreach ($actions as $key => $action) {
        if (!$action) {
            continue;
        }

        $acs[$key] = [
            'title' => ucwords(str_replace('_', ' ', $key)),
            'route' => $route . '.' . $key,
            'extraclass' => $key == 'delete' ? 'confirm-delete' : '',
        ];
    }

    return $acs;
}

function getTableColumns($table, $skip = [], $showcoltype = false)
{
    $columns = DB::getSchemaBuilder()->getColumnListing($table);
    if (!empty($skip)) {
        $columns = array_diff($columns, $skip);
    }
    $cols = [];
    foreach ($columns as $key => $column) {
        $cols[$column] = ucwords(str_replace('_', ' ', $column));
    }
    return $cols;
}

function createField($field, $type = 'text', $label = '', $placeholder = '', $required = false, $value = '', $col = 12, $extra = '')
{
    $field = [
        'type' => $type,
        'name' => $field,
        'label' => $label,
        'placeholder' => $placeholder,
        'required' => $type == 'file' ? false : $required,
        'value' => $value,
        'col' => $col,
        'class' => $field,
        'extra' => $extra,
    ];

    return $field;
}

function getFieldType($type)
{
    $type = strtolower($type);
    if (strpos($type, 'email') !== false) {
        return 'email';
    } elseif (strpos($type, 'password') !== false) {
        return 'password';
    } elseif (strpos($type, 'image') !== false || strpos($type, 'photo') !== false || strpos($type, 'avatar') !== false || strpos($type, 'logo') !== false || strpos($type, 'banner') !== false) {
        return 'file';
    }
}

function imageCheck($request)
{
    //if image, logo, photo, avatar, banner
    $key = 'image';
    if ($request->hasFile('image')) {
        $key = 'image';
    } elseif ($request->hasFile('logo')) {
        $key = 'logo';
    } elseif ($request->hasFile('photo')) {
        $key = 'photo';
    } elseif ($request->hasFile('avatar')) {
        $key = 'avatar';
    } elseif ($request->hasFile('banner')) {
        $key = 'banner';
    } elseif ($request->hasFile('icon')) {
        $key = 'icon';
    } else {
        return false;
    }

    return $key;
}
function checkIfHtml($string)
{
    if (strpos($string, '<') !== false && strpos($string, '>') !== false && strpos($string, '/') !== false) {
        return true;
    }
    return false;
}

function renderImage($image = '', $small = true)
{
    $src = asset('logo.jpg');
    $class = 'img-fluid';
    $style = "height: 100px; width: 100px;";
    if (!empty($image)) {
        if (!$small) {
            $style = "height: 200px; width: 200px;";
        }
        $src = asset($image);
    }
    return view('htmls.elements.image', compact('src', 'class', 'style'))->render();
}

function getFormFields($table, $skip = [], $user = '', $extra = '')
{
    if (!empty($user) && is_array($user)) {
        $user = (object) $user;
    }
    $fields = getTableColumns($table, $skip, true);
    $form = [];
    foreach ($fields as $key => $field) {
        $key1 = ucwords(str_replace('_', ' ', $key));
        $form[$key] = createField($key, getFieldType($key), $field, $field, true, $user->$key ?? '', $extra);
    }
    return $form;
}

function getMenus()
{
    $men = Menu::orderBy('order', 'asc')->get();
    return $men;
}
