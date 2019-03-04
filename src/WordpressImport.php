<?php

namespace Daikazu\LaravelBlog;


use Illuminate\Support\Facades\Hash;

class WordpressImport
{

    const USER_DEFAULT_PASSWORD = 'password';
    const USER_DEFAULT_ROLE_ID = '2';

    public $authors;
    public $attachments;
    public $categories;
    public $posts;
    public $pages;

    private $xml;
    private $copyImages;
    private $timeout;

    public function __construct($xml, $copyImages, $timeout)
    {
        $this->xml = $xml;
        $this->copyImages = $copyImages;
        $this->timeout = $timeout;

        set_time_limit($this->timeout);
        ini_set('max_execution_time', $this->timeout);
        ini_set('default_socket_timeout', $this->timeout);
        ini_set('memory_limit', '256M'); // Needed for large images

        $this->xml = simplexml_load_file($this->xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $this->import();
    }

    private function import()
    {

        $this->saveAuthors();
        $this->saveCategories();
        $this->saveAttachments();
        $this->savePostsAndPages('post');
//        $this->savePostsAndPages('page');


    }

    /**
     *  Create new users and load them into array
     */
    private function saveAuthors()
    {
        $wpData = $this->xml->channel->children('wp', true);

        //TODO CHECK FOR USER ROLES AND ASSIGN DEFAULT

        foreach ($wpData->author as $author) {
            $this->authors[(string)$author->author_login] = [
//                'role_id'  => SELF::USER_DEFAULT_ROLE_ID,
                'name'     => (string)$author->author_display_name,
                'email'    => (string)$author->author_email,
                'password' => Hash::make(SELF::USER_DEFAULT_PASSWORD),
            ];

            $new_user = new User($this->authors[(string)$author->author_login]);
            $new_user->save();

            // store the new id in the array
            $this->authors[(string)$author->author_login]['id'] = $new_user->id;
        }
    }

    /**
     *  Create new categories and store them in the array
     */
    private function saveCategories()
    {
        $wpData = $this->xml->channel->children('wp', true);

        //TODO: Category order after implementation

        $order = 1;

        foreach ($wpData->category as $category) {
            $this->categories[(string)$category->category_nicename] = [
                'parent_id' => null,
                'order'     => $order,
                'name'      => (string)$category->cat_name,
                'slug'      => (string)$category->category_nicename,
            ];

            $new_cat = new Category($this->categories[(string)$category->category_nicename]);
            $new_cat->save();

            $this->categories[(string)$category->category_nicename]['id'] = $new_cat->id;

            $this->categories[(string)$category->category_nicename]['parent'] = (string)$category->category_parent;
            $order += 1;
        }


        // Save any parent categories to their children
        foreach ($this->categories as $category) {
            if (!empty($category['parent'])) {
                $parent = Category::where('slug', '=', $category['parent'])->first();
                if (isset($parent->id)) {
                    $category['parent_id'] = $parent->id;
                    $this_cat = Category::find($category['id']);
                    if (isset($this_cat->id)) {
                        $this_cat->parent_id = $parent->id;
                        $this_cat->save();
                    }
                }
            }
        }


    }

    private function saveAttachments()
    {

        foreach ($this->xml->channel->item as $item) {
            // Save The Attachments in an array
            $wpData = $item->children('wp', true);
            if ($wpData->post_type == 'attachment') {
                $this->attachments[(string)$wpData->post_parent] = (string)$wpData->attachment_url;
            }
        }

    }

    private function savePostsAndPages(string $type = 'post')
    {

        foreach ($this->xml->channel->item as $item) {
            $wpData = $item->children('wp', true);
            $content = $item->children('content', true);
            $excerpt = $item->children('excerpt', true);
            $category = null;

            $image = isset($this->attachments[(string)$wpData->post_id]) ? $this->attachments[(string)$wpData->post_id] : '';

            $dc = $item->children('dc', true);
            $author = null;
            $slug = (string)$wpData->post_name;

            if (isset($dc->creator)) {
                $author = (string)$dc->creator;
            }

            if (isset($item->category["nicename"])) {
                $category = (string)$item->category["nicename"];
            }

            if ($type == 'post') {
                $status = 'PUBLISHED';
                if (isset($wpData->status) && $wpData->status != 'publish') {
                    $status = 'DRAFT';
                }
                if (empty($slug)) {
                    $slug = 'post-' . (string)$wpData->post_id;
                }
            } elseif ($type == 'page') {
                $status = 'ACTIVE';
                if (isset($wpData->status) && $wpData->status != 'publish') {
                    $status = 'INACTIVE';
                }
                if (empty($slug)) {
                    $slug = 'page-' . (string)$wpData->post_id;
                }
            }

            if ($wpData->post_type == $type) {
                if ($type == 'post') {

                    $post = new Post([
                        "user_id"      => isset($this->authors[$author]['id']) ? $this->authors[$author]['id'] : 1,
                        "category_id"  => isset($this->categories[$category]['id']) ? $this->categories[$category]['id'] : null,
                        "title"        => trim((string)$item->title, '"'),
                        "slug"         => $slug,
                        "excerpt"      => trim((string)$excerpt->encoded, '" \n'),
                        "body"         => $this->autop(trim((string)$content->encoded, '" \n')),
                        "seo_title"    => trim((string)$item->title, '"'),
                        "featured"     => 0,
                        "is_published" => ($status == 'ACTIVE') ? true : false,
                        "publish_at"   => \Carbon\Carbon::parse((string)$wpData->post_date),
                        "created_at"   => \Carbon\Carbon::parse((string)$wpData->post_date),
                        "updated_at"   => \Carbon\Carbon::parse((string)$wpData->post_date),
                    ]);
                    $post->save();

                    // Save images
                    if (!empty($image) && $this->copyImages) {
                        $post->addMediaFromUrl($image)->toMediaCollection('image')->responsiveImages();
                    }

                } elseif ($type == 'page') {
                    $this->pages[] = [
//                        "author_id"		=> isset($this->authors[$author]['id']) ? $this->authors[$author]['id'] : 1,
//                        "title"			=> trim((string)$item->title, '"'),
//                        "excerpt"		=> trim((string)$excerpt->encoded, '" \n'),
//                        "body"			=> $this->autop(trim((string)$content->encoded, '" \n')),
//                        "image"			=> $this->getImage($image),
//                        "slug"			=> $slug,
//                        "status"		=> $status,
//                        "created_at"	=> \Carbon\Carbon::parse((string)$item->pubDate),
//                        "updated_at"	=> \Carbon\Carbon::parse((string)$item->pubDate),
                    ];

                }
            }


        }




    }

    private function autop($pee, $br = true)
    {
        $pre_tags = [];
        if (trim($pee) === '') {
            return '';
        }
        $pee = $pee . "\n"; // just to make things a little easier, pad the end
        if (strpos($pee, '<pre') !== false) {
            $pee_parts = explode('</pre>', $pee);
            $last_pee = array_pop($pee_parts);
            $pee = '';
            $i = 0;
            foreach ($pee_parts as $pee_part) {
                $start = strpos($pee_part, '<pre');
                // Malformed html?
                if ($start === false) {
                    $pee .= $pee_part;
                    continue;
                }
                $name = "<pre wp-pre-tag-$i></pre>";
                $pre_tags[$name] = substr($pee_part, $start) . '</pre>';
                $pee .= substr($pee_part, 0, $start) . $name;
                $i++;
            }
            $pee .= $last_pee;
        }
        $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
        // Space things out a little
        $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|option|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|noscript|samp|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
        $pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
        $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
        $pee = str_replace(["\r\n", "\r"], "\n", $pee); // cross-platform newlines
        if (strpos($pee, '<object') !== false) {
            $pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee); // no pee inside object/embed
            $pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
        }
        $pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
        // make paragraphs, including one at the end
        $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
        $pee = '';
        foreach ($pees as $tinkle) {
            $pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
        }
        $pee = preg_replace('|<p>\s*</p>|', '',
            $pee); // under certain strange conditions it could create a P of entirely whitespace
        $pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
        $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
        $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
        $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
        if ($br) {
            $pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', function ($matches) {
                return str_replace("\n", "<PreserveNewline />", $matches[0]);
            }, $pee);
            $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
            $pee = str_replace('<PreserveNewline />', "\n", $pee);
        }
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
        $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
        $pee = preg_replace("|\n</p>$|", '</p>', $pee);
        if (!empty($pre_tags)) {
            $pee = str_replace(array_keys($pre_tags), array_values($pre_tags), $pee);
        }
        return $pee;
    }


}
