@extends('frontend.layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>{{ __t('contact_us') }}</h1>
        <form action="{{ route('contact.submit') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">{{ __t('your_name') }}</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="form-group mt-3">
                <label for="email">{{ __t('your_email') }}</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group mt-3">
                <label for="message">{{ __t('your_message') }}</label>
                <textarea name="message" id="message" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary mt-3">{{ __t('send_message') }}</button>
        </form>
    </div>
@endsection
