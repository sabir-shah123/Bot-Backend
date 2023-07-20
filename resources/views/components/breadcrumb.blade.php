<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
    @foreach ($breadcrumb as $ar)
        <li class="breadcrumb text-muted "><a href="{{ $ar['url'] }}"
                class="text-muted text-hover-primary">{{ $ar['name'] }}</a></li>
        @if (!$loop->last)
            <li class="breadcrumb-item pl-2" style="margin-left: 5px;">
                <span class="bullet bg-gray-400 w-5px h-2px"></span>
            </li>
        @endif
    @endforeach
</ul>
