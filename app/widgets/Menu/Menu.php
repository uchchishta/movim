<?php

include_once WIDGETS_PATH.'Post/Post.php';

class Menu extends \Movim\Widget\Base
{
    private $_paging = 15;

    function load()
    {
        $this->registerEvent('post', 'onPost');
        $this->registerEvent('post_retract', 'onRetract', 'news');
        $this->registerEvent('pubsub_postdelete', 'onRetract', 'news');

        $this->addjs('menu.js');
    }

    function onRetract($packet)
    {
        $this->ajaxGetAll();
    }

    function onStream($count)
    {
        $view = $this->tpl();
        $view->assign('count', $count);
        $view->assign('refresh', $this->call('ajaxGetAll'));

        $this->rpc('movim_posts_unread', $count);
        $this->rpc('MovimTpl.fill', '#menu_refresh', $view->draw('_menu_refresh', true));
    }

    function onPost($packet)
    {
        $since = \App\Cache::c('since');

        if ($since) {
            $count = \App\Post::whereIn('id', function ($query) {
                $query = $query->select('id')->from('posts');
                $query = \App\Post::withContactsScope($query);
                $query = \App\Post::withMineScope($query);
                $query = \App\Post::withSubscriptionsScope($query);
            })->withoutComments()->where('published', '>', $since)->count();
        } else {
            $count = 0;
        }

        $post = $packet->content;

        if (!is_object($post)) return;

        $post = \App\Post::where('server', $post->server)
                         ->where('node', $post->node)
                         ->where('nodeid', $post->nodeid)
                         ->first();

        if ($post->isComment()
        && !$post->isMine()) {
            $contact = \App\Contact::firstOrNew(['id' => $post->aid]);
            Notification::append(
                'news',
                $contact->truename,
                $post->title,
                $contact->getPhoto('s'),
                2
            );
        } elseif ($count > 0
        && (strtotime($post->published) > strtotime($since))) {
            if ($post->isMicroblog()) {
                $contact = \App\Contact::firstOrNew(['id' => $post->origin]);

                $title = ($post->title == null)
                    ? __('post.default_title')
                    : $post->title;

                if (!$post->isMine()) {
                    Notification::append(
                        'news',
                        $contact->truename,
                        $title,
                        $contact->getPhoto('s'),
                        2,
                        $this->route('post', [$post->origin, $post->node, $post->nodeid]),
                        $this->route('contact', $post->origin)
                    );
                }
            } else {
                $logo = ($post->logo) ? $post->getLogo() : null;

                Notification::append(
                    'news',
                    $post->title,
                    $post->node,
                    $logo,
                    2,
                    $this->route('post', [$post->origin, $post->node, $post->nodeid]),
                    $this->route('community', [$post->origin, $post->node])
                );
            }

            $this->onStream($count);
        }
    }

    function ajaxGetAll($page = 0)
    {
        $this->ajaxGet('all', null, null, $page);
    }

    function ajaxGetNews($page = 0)
    {
        $this->ajaxGet('news', null, null, $page);
    }

    function ajaxGetFeed($page = 0)
    {
        $this->ajaxGet('feed', null, null, $page);
    }

    function ajaxGetNode($server = null, $node = null, $page = 0)
    {
        $this->ajaxGet('node', $server, $node, $page);
    }

    function ajaxGetMe($page = 0)
    {
        $this->ajaxGet('me', null, null, $page);
    }

    function ajaxGet($type = 'all', $server = null, $node = null, $page = 0)
    {
        $html = $this->prepareList($type, $server, $node, $page);

        if ($page > 0) {
            $this->rpc('MovimTpl.append', '#menu_wrapper', $html);
        } else {
            $this->rpc('MovimTpl.fill', '#menu_widget', $html);
        }

        $this->rpc('MovimUtils.enhanceArticlesContent');
        $this->rpc('Menu.refresh');
    }

    function prepareList($type = 'all', $server = null, $node = null, $page = 0)
    {
        $view = $this->tpl();

        $posts = \App\Post::whereIn('id', function ($query) {
            $query = $query->select('id')->from('posts');
            $query = \App\Post::withContactsScope($query);
            $query = \App\Post::withMineScope($query);
            $query = \App\Post::withSubscriptionsScope($query);
        });

        $since = \App\Cache::c('since');

        $count = ($since)
            ? $posts->where('published', '>', $since)->count()
            : 0;

        // getting newer, not older
        if ($page == 0){
            $count = 0;
            $last = $posts->orderBy('published', 'desc')->first();
            \App\Cache::c('since', ($last) ? $last->published : date(SQL_DATE));
        }

        $items = \App\Post::skip($page * $this->_paging + $count)->withoutComments();

        $items->whereIn('id', function ($query) use ($type) {
            $query = $query->select('id')->from('posts');

            if (in_array($type, ['all', 'feed'])) {
                $query = \App\Post::withContactsScope($query);
                $query = \App\Post::withMineScope($query);
            }

            if (in_array($type, ['all', 'news'])) {
                $query = \App\Post::withSubscriptionsScope($query);
            }

        });

        $next = $page + 1;

        $view->assign('history', $this->call('ajaxGetAll', $next));

        if ($type == 'news') {
            $view->assign('history', $this->call('ajaxGetNews', $next));
        } elseif ($type == 'feed') {
            $view->assign('history', $this->call('ajaxGetFeed', $next));
        }

        $view->assign('items', $items
            ->orderBy('published', 'desc')
            ->take($this->_paging)->get());
        $view->assign('type', $type);
        $view->assign('page', $page);
        $view->assign('paging', $this->_paging);

        return $view->draw('_menu_list', true);
    }

    function preparePost($post)
    {
        return (new \Post)->preparePost($post, false, true);
    }
}
