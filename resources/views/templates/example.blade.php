<h1>{{ $page->title }}</h1>

<img src="{{ url('storage/'.$page->banner) }}">  

{!! $page->content !!}