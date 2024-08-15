<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;
use app\models\Comment;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property string|null $content
 * @property string|null $date
 * @property string|null $image
 * @property int|null $viewed
 * @property int|null $user_id
 * @property int|null $status
 * @property int|null $category_id
 *
 * @property ArticleTag[] $articleTags
 * @property Comment[] $comments
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title', 'description', 'content'], 'string'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['date'], 'default', 'value' => date('Y-m-d')],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'content' => 'Content',
            'date' => 'Date',
            'image' => 'Image',
            'viewed' => 'Viewed',
            'user_id' => 'User ID',
            'status' => 'Status',
            'category_id' => 'Category ID',
        ];
    }
    
    public function saveImage($filename) {
        $this->image = $filename;
        return $this->save(false);
    }
    
    public function getImage() {
        return ($this->image) ? '/uploads/' . $this->image : '/no-image.jpg';
    }
    
    public function deleteImage() {
        $imageUploadModel = new ImageUpload();
        $imageUploadModel->deleteCurrentImage($this->image);
    }
    
    public function beforeDelete() {
        $this->deleteImage();
        return parent::beforeDelete();
    }
    
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }
    
    public function saveCategory($category_id) {
        $category = Category::findOne($category_id);
        
        if($category != null) {
            $this->link('category', $category);
            return true;
        }
    }
    
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable('article_tag', ['article_id' => 'id']);
    }
    
    public function getSelectedTags() {
        $selectedTags = $this->getTags()->select('id')->asArray()->all();
        return ArrayHelper::getColumn($selectedTags, 'id');
    }
    
    public function saveTags($tags)
    {
        if (is_array($tags))
        {
            $this->clearCurrentTags();

            foreach($tags as $tag_id)
            {
                $tag = Tag::findOne($tag_id);
                $this->link('tags', $tag);
            }
        }
    }

    public function clearCurrentTags()
    {
        ArticleTag::deleteAll(['article_id'=>$this->id]);
    }
    
    public function getDate() {
        return Yii::$app->formatter->asDate($this->date);
    }
    
    public static function getAll($pageSize = 5) {
        $query = Article::find();
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $articles = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        
        $data['articles'] = $articles;
        $data['pagination'] = $pagination;
        
        return $data;
    }
    
    public static function getPopular() {
        return Article::find()->orderBy('viewed desc')->limit(3)->all();
    }
    
    public static function getRecent() {
        return Article::find()->orderBy('date asc')->limit(4)->all();
    }
    
    public function getComments() {
        return $this->hasMany(Comment::class, ['article_id'=>'id']);
    }
    
    public function getArticleComments() {
        return $this->getComments()->where(['status' => 1])->all();
    }
    
    public function getAuthor() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    
    public function saveArticle() {
        $this->user_id = Yii::$app->user->id;
        
        return $this->save();
    }
    
    public function viewedCounter() {
        $this->viewed += 1;
        return $this->save(false);
    }
    
    /**
     * Gets query for [[ArticleTags]].
     *
     * @return \yii\db\ActiveQuery
     */
    /*
    public function getArticleTags()
    {
        return $this->hasMany(ArticleTag::class, ['article_id' => 'id']);
    }
    */

    /**
     * Gets query for [[Comments]].
     *
     * @return \yii\db\ActiveQuery
     */
    /*
    public function getComments()
    {
        return $this->hasMany(Comment::class, ['article_id' => 'id']);
    }
    */
}
