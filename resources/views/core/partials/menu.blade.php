@php
    $active = $builder->isActive($item);

    $layer = $item['layer'] ?? 0;
@endphp

    @if(isset($item['is_header']))
        <li class="nav-header">
            {{ $item['title'] }}
        </li>
    @elseif(! isset($item['children']))
        <li class="nav-item">
            <a href="{{ $builder->getUrl($item['uri']) }}" class="nav-link {!! $builder->isActive($item) ? 'active' : '' !!}">
                {!! str_repeat('&nbsp;', $layer) !!}<i class="fa {{ $item['icon'] ?: 'feather icon-circle' }}"></i>
                <p>
                    {{ $item['title'] }}
                </p>
            </a>
        </li>
    @else
        @php
            $active = $builder->isActive($item);
        @endphp

        <li class="nav-item has-treeview {{ $active ? 'menu-open' : '' }}">
            <a href="#" class="nav-link">
                {!! str_repeat('&nbsp;', $layer) !!}<i class="fa {{ $item['icon'] ?: 'feather icon-circle' }}"></i>
                <p>
                    {{ $item['title'] }}
                    <i class="right fa fa-angle-left"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                @foreach($item['children'] as $item)
                    @php
                        $item['layer'] = $layer + 1;
                    @endphp

                    @include('core.partials.menu', $item)
                @endforeach
            </ul>
        </li>
    @endif

