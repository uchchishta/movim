<?php

namespace App;

use CoenJacobs\EloquentCompositePrimaryKeys\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Model;
use Movim\Picture;

class Roster extends Model
{
    use HasCompositePrimaryKey;

    public $incrementing = false;
    protected $primaryKey = ['session_id', 'jid'];
    protected $fillable = ['jid', 'name', 'ask', 'subscription', 'group'];

    protected $attributes = [
        'session_id'    => SESSION_ID
    ];

    public function session()
    {
        return $this->hasOne('App\Session');
    }

    public function contact()
    {
        return $this->hasOne('App\Contact', 'id', 'jid');
    }

    public function presences()
    {
        return $this->hasMany('App\Presence', 'jid', 'jid')
                    ->where('session_id', $this->session_id);
    }

    public function presence()
    {
        return $this->hasOne('App\Presence', 'jid', 'jid')
                    ->where('session_id', $this->session_id)
                    ->orderBy('value');
    }

    public function set($stanza)
    {
        $this->jid = (string)$stanza->attributes()->jid;

        $this->name = (isset($stanza->attributes()->name)
            && !empty((string)$stanza->attributes()->name))
            ? (string)$stanza->attributes()->name
            : null;

        $this->ask = $stanza->attributes()->ask
            ? (string)$stanza->attributes()->ask
            : null;

        $this->subscription = $stanza->attributes()->subscription
            ? (string)$stanza->attributes()->subscription
            : null;

        $this->group = $stanza->group
            ? (string)$stanza->group
            : null;
    }

    public static function saveMany(array $rosters)
    {
        $now = \Carbon\Carbon::now();
        $rosters = collect($rosters)->map(function (array $data) use ($now) {
            return array_merge([
                'created_at' => $now,
                'updated_at' => $now,
            ], $data);
        })->all();

        return Roster::insert($rosters);
    }

    public function getSearchTerms()
    {
        return cleanupId($this->jid).'-'.
            cleanupId($this->group);
    }

    public function getPhoto($size = 'l')
    {
        $sizes = [
            'wall'  => [1920, 1080],
            'xxl'   => [1280, 300],
            'xl'    => [512 , false],
            'l'     => [210 , false],
            'm'     => [120 , false],
            's'     => [50  , false],
            'xs'    => [28  , false],
            'xxs'   => [24  , false]
        ];


        $p = new Picture;
        return $p->get($this->jid, $sizes[$size][0], $sizes[$size][1]);
    }

    public function getTruenameAttribute()
    {
        if ($this->name && !filter_var($this->name, FILTER_VALIDATE_EMAIL)) return $this->name;
        if ($this->contact && $this->contact->truename) {
            return $this->contact->truename;
        }

        return explodeJid($this->jid)['username'];
    }
}
