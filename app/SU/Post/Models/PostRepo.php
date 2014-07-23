<?php namespace SU\Post\Models;

use SU\Core\Repository\BaseRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostRepo extends BaseRepository {

	public function __construct(Post $post){

		$this->model = $post;
	}

	/**
	 * id автора поста
	 *
	 * @param int $post_id
	 * @return int
	 */
	public function getAuthorId($post_id)
	{
		$author_id = $this->model->where("id", $post_id)->first(["author_id"])->author_id;
		return $author_id;
	}
	public function getAuthorIdBySlug($slug)
	{
		$author_id = $this->model->where("slug", $slug)->first(["author_id"])->author_id;
		return $author_id;
	}

	/**
	 * Получить пост по урлу
	 *
	 * @param $slug
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	public function getBySlug($slug)
	{
		$post = $this->model->where("slug", $slug)->with("author")->first();
		//$post = $this->model->where("slug", $slug)->with("author")->first();
		return $post;
	}

	public function getAllLastPosts($num = 10)
	{
		return $this->model->notDraft()->orderBy("published_at", "DESC")->get();
	}

}