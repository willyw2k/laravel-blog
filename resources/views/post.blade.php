<h1 style="text-align: center">{{$post->title}}</h1>

<div>
    @if( count($post->getMedia('image')))
        {{ $post->getFirstMedia('image')('responsive', [
        'class' => '',
         'alt' => $post->title,
         'style' => 'display:block; max-width:50%; height:auto; margin-left: auto; margin-right: auto;'
        ] ) }}
    @endif
</div>


<div style="max-width:50%; margin-left: auto; margin-right: auto; ">
    {!! $post->body !!}
</div>

