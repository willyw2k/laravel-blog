<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Trix;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Benjaminhirsch\NovaSlugField\Slug;
use Laravel\Nova\Fields\BelongsToMany;
use Benjaminhirsch\NovaSlugField\TextWithSlug;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;

class Post extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'Daikazu\\LaravelBlog\\Post';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'title';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'title',
        'excerpt',
        'body',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),

            TextWithSlug::make('Title', 'title')
                ->slug('Slug')
                ->rules('required'),

            Slug::make('Slug')
                ->help('Post URL slug (i.e. /blog/my-post-title)')
                ->hideFromIndex()
                ->rules('required'),

            Images::make('Main Image', 'image')
                ->thumbnail('thumb'),

            Trix::make('Body', 'body')
                ->rules('required'),

            Textarea::make('Excerpt', 'excerpt')
                ->help('A small snippet of text usual displayed on the Blog index')
                ->hideFromIndex(),

            Textarea::make('Meta Description', 'meta_description')
                ->hideFromIndex(),

            Text::make('Seo Title', 'seo_title')
                ->hideFromIndex(),

            DateTime::make('Publish At', 'publish_at')->sortable(),

            DateTime::make('Publish Until', 'publish_until')
                ->sortable()
                ->hideFromIndex(),

            Boolean::make('Is Published', 'is_published')->sortable(),

            Boolean::make('Featured', 'featured')
                ->rules('required')
                ->sortable(),

            BelongsTo::make('Category')->nullable(),

            BelongsTo::make('User', 'user')->nullable(),

            BelongsToMany::make('Tags', 'Tags'),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
