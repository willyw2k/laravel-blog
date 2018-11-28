<h1 style="text-align: center;">{{$category->name}}</h1>

<div style="display: flex; flex-wrap: wrap">


    @foreach($posts as $post)
        <div style="width: 33.3%;">

            <div style="padding: 4px; text-align: center;">{{$post->title}}</div>
            @if( count($post->getMedia('image')))
                {{ $post->getFirstMedia('image')('responsive', [
                'class' => '',
                 'alt' => $post->title,
                 'style' => 'width:100%; height:auto;'
                ] ) }}
            @endif

            <p style="padding:4px;">
                {{$post->excerpt}}
            </p>

            <a href="{{url( config('laravel-blog.prefix').'/'. $post->slug)}}">Read More...</a>

        </div>
    @endforeach


</div>


<div class="col-sm-12">
    <div class="text-center">
        {{$posts->links()}}
    </div>
</div>




@push('meta')
    @if($posts->previousPageUrl())
        <link rel="prev" href="{{$posts->previousPageUrl()}}">
    @endif
    @if($posts->nextPageUrl())
        <link rel="next" href="{{$posts->nextPageUrl()}}">
    @endif
@endpush
