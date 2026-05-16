@extends('layouts.blank')

@section('body')
  <main class="mx-auto max-w-3xl px-4 sm:px-6 py-20">
    <h1 class="text-3xl font-bold">NurSync Demo</h1>
    <p class="mt-3 text-slate-600">This is a sample page to show the smooth navigation animation.</p>
    <a href="{{ url('/') }}" class="btn-ghost-light mt-6 inline-flex">← Back to Home</a>
  </main>
@endsection
