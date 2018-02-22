<?php namespace Aike\Web\Article;

use Aike\Web\Index\BaseModel;

class Article extends BaseModel
{
    protected $table = 'article';

    public function user()
    {
        return $this->belongsTo('Aike\Web\User\User', 'created_by');
    }

    public function category()
    {
        return $this->belongsTo('Aike\Web\Article\ArticleCategory');
    }
}
