{{-- Sidebar / Hero Categories column (use inside hero section in home.blade) --}}
<div class="col-lg-3">
    <div class="hero__categories">
        <div class="hero__categories__all">
            <i class="fa fa-bars"></i>
            <span>{{ __t('all_departments') }}</span>
        </div>

        <ul>
            @foreach ($categories as $cat)
                <li><a href="{{ route('frontend.categories.show', $cat->id) }}">{{ $cat->name }}</a></li>
            @endforeach
        </ul>
    </div>
</div>