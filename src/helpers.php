<?php

if (!function_exists('get_blog_excerpt')) {
    /**
     * @param $content
     * @param null $length
     * @param string $more
     * @return string
     */
    function get_blog_excerpt($content, $length = null, $more = '...')
    {
        $excerpt = strip_tags(trim($content));
        $words = str_word_count($excerpt, 2);
        if ($length !== null) {
            if (count($words) > $length) {
                $words = array_slice($words, 0, $length, true);
                end($words);
                $position = key($words) + strlen(current($words));
                $excerpt = substr($excerpt, 0, $position) . $more;
            }
        }

        return $excerpt;
    }
}

if (!function_exists('get_blog_word_count')) {
    /**
     * @param $content
     * @return mixed
     */
    function get_blog_word_count($content)
    {
        $excerpt = strip_tags(trim($content));

        return str_word_count($excerpt, 0);
    }
}

if (!function_exists('demote_html_header_tags')) {
    /**
     * @param string $html
     * @return mixed
     */
    function demote_html_header_tags(string $html)
    {
        $originalHeaderTags = [];
        $demotedHeaderTags = [];

        foreach (range(100, 1) as $index) {
            $originalHeaderTags[] = '<h' . $index . '>';

            $originalHeaderTags[] = '</h' . $index . '>';

            $demotedHeaderTags[] = '<h' . ($index + 1) . '>';

            $demotedHeaderTags[] = '</h' . ($index + 1) . '>';
        }

        return str_ireplace($originalHeaderTags, $demotedHeaderTags, $html);
    }
}
