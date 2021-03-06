<?php

namespace App\Http\Controllers\Agent\kb;

// Controllers
use App\Http\Controllers\Common\KnowledgeBaseController;
// Requests
use App\Http\Controllers\Controller;
use App\Http\Requests\kb\ArticleRequest;
use App\Http\Requests\kb\ArticleTemplateRequest;
// Models
use App\Model\helpdesk\Settings\System;
use App\Model\kb\Article;
use App\Model\kb\ArticleTemplate;
use App\Model\kb\Category;
use App\Model\kb\Comment;
// Classes
use App\Model\kb\Relationship;
use App\Model\kb\Settings;
use App\User;
use Auth;
use Datetime;
use DateTimeZone;
use Exception;
use Illuminate\Http\Request;
use Lang;
use App\Model\helpdesk\Filters\Tag;
use App\Model\kb\KbArticleTag;

/**
 * ArticleController
 * This controller is used to CRUD Articles.
 *
 * @author       Arindam Jana <arindam.jana@ladybirdweb.com>
 */
class ArticleController extends Controller
{

    /**
     * Create a new controller instance.
     * constructor to check
     * 1. authentication
     * 2. user roles
     * 3. roles must be agent.
     *
     * @return void
     */

    public function __construct()
    {
        // checking authentication
        $this->middleware(['auth', 'role.agent']);
        SettingsController::language();
    }

    /**
     * This method return all article Info with pagination
     * @param Request $request
     * @return type json
     * Advance search we have to implement in this method
     */
    public function getData(Request $request)
    {
       $pagination = $request->input('pagination') ? :10;
          $sortBy = $request->input('sort-by') ? : 'id';
          $search = $request->input('search-query');
          $baseQuery = Article::with('categories:kb_category.id,name','tags:tags.id,name')->with('author:users.id,first_name,last_name,user_name,email')->select('id', 'name', 'description', 'publish_time', 'slug','author')->orderBy($sortBy, 'desc');
        $searchQuery = $baseQuery->withCount('allComments')->withCount('pendingComments')->where(function($q) use ($search) {
                            $q->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('slug', 'LIKE', '%' . $search . '%')
                            ->orWhere('description', 'LIKE', '%' . $search . '%');
                        })
                   ->paginate($pagination);
               return successResponse($searchQuery);

    }

    /**
     * List of Articles.
     *
     * @return type html
     */
    public function index()
    {
        /* show article list */
        try {

            return view('themes.default1.agent.kb.article.index');
        } catch (Exception $ex) {

            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Creating a Article.
     *
     * @return type html
     */
    public function create()
    {
        /* get the create page  */
        try {

            return view('themes.default1.agent.kb.article.create');
        } catch (Exception $ex) {

            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * This method return category and template info
     * @return type json
     */
    public function createArticle()
    {
        try {
            /* get the attributes of the category */
            $categories = Category::where('status', 1)->orderBy('id', 'desc')->select('id', 'name')->get()->toArray();
            /* get the attributes of the category */
            $templates = ArticleTemplate::where('status', 1)->select('id', 'name')->get()->toArray();

            return successResponse(['categorys' => $categories, 'templates' => $templates]);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Insert or update the values to the article.
     *
     * @param type ArticleRequest $request
     *
     * @return type message
     */
    public function store(ArticleRequest $request)
    {
        try {
            //check article description content is present or not

            $content = trim(preg_replace("/<img[^>]+\>/i", "", $request->description), " \t.");

            if (strip_tags($content) == "\r\n" || strip_tags($content) == "\n") {
                return errorResponse(Lang::get('lang.please_put_description_contain'));
            }

            // requesting the values to store article data
            $time = $request->input('year') . '-' . $request->input('month') . '-' . $request->input('day') . ' ' . $request->input('hour') . ':' . sprintf("%02d", $request->input('minute')) . ':00';

            $scheduleDate = new DateTime($time, new DateTimeZone(System::first()->time_zone));
            $scheduleDate->setTimeZone(new DateTimeZone('UTC'));
            $publishTime = $scheduleDate->format('Y-m-d H:i:s');
            $template = '';

            $article = Article::updateOrCreate(
                ['id' => $request->articleid], ['name' => $request->name, 'slug' => $request->slug, 'publish_time' => $publishTime, 'created_at' => $publishTime, 'updated_at' => $publishTime, 'type' => $request->type, 'description' => $request->description, 'visible_to' => $request->visible_to, 'author' => $request->author, 'template' => $template]
            );

            $articleId = $request->articleid ? $request->articleid : $article->id;
            // creating article category relationship
            $article->categories()->sync(explode(",", $request->input('category_id')));

            //creating article tag relationship
            $article->tags()->sync(explode(",", $request->input('tag_id')));

            /* insert the values to the article table  */

            $checkSeo = Article::where('id', $articleId)->first();
            //remove new line
            $string = trim(preg_replace('/\s\s+/', ' ', strip_tags($checkSeo['description'])));
            //remove space
            $filterOutput = trim(str_replace("&nbsp;", '', $string));

            $checkSeo->meta_description = (!$checkSeo->meta_description) ? substr($filterOutput, 0, 160) . "..." : $request->meta_description;

            $checkSeo->seo_title = (!$checkSeo->seo_title) ? $checkSeo->name : $request->seo_title;
            $checkSeo->save();

            $outputMessage = ($request->articleid) ? Lang::get('lang.article_updated_successfully') : Lang::get('lang.article_saved_successfully');

            return successResponse($outputMessage);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * This method return view article edit page
     *
     * @return html
     */
    public function edit()
    {
        try {
            return view('themes.default1.agent.kb.article.edit');
        } catch (Exception $ex) {

            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * This method return specific article information
     * @param  int $articleId
     * @return json
     */
    public function editArticle($articleId)
    {
        try {
            $article = Article::whereId($articleId)->with(['tags:tags.id,name'])->first()->toArray();

           $author = User::where('id', $article['author'])->select('id','first_name','last_name','email','user_name','profile_pic')->first();

            $article['author'] = (['id' => $author->id, 'name' => $author->fullName, 'profile_pic' => $author->profile_pic]);

            $catIds = Article::where('id', $articleId)->first()->categories()->pluck('category_id')->toArray();
            $article['category'] = Category::whereIn('id', $catIds)->orderBy('id', 'desc')->select('id', 'name')->get()->toArray();

            $publishTime = new DateTime($article['publish_time']);
            $publishTime->setTimezone(new DateTimeZone(System::first()->time_zone));
            $article['publish_time'] = $publishTime->format("Y-m-d H:i:s");
            $article['template'] = ($article['template']) ? ArticleTemplate::where('id', $article['template'])->select('id', 'name')->first()->toArray() : null;
            $article['description'] = Article::whereId($articleId)->first()->getOriginal('description');

            return successResponse(['article' => $article]);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Delete an Article by slug.
     *
     * @param type string   $slug
     * @param type Article $article
     *
     * @return message
     */
    public function destroy($slug, Article $article)
    {

        /* delete the selected article from the table */
        $article = $article->where('slug', $slug)->first(); //get the selected article via id
        Comment::where('article_id', $article->id)->delete();
        Relationship::where('article_id', $article->id)->delete();
        KbArticleTag::where('article_id', $article->id)->delete();


        if ($article) {
            if ($article->delete()) { //true:redirect to index page with success message
                return successResponse(Lang::get('lang.article_deleted_successfully'));
            } else { //redirect to index page with fails message
                return errorResponse(Lang::get('lang.article_not_deleted'));
            }
        } else {
            return errorResponse(Lang::get('lang.article_can_not_deleted'));
        }
    }

    /**
     * user time zone
     * fetching time zone.
     *
     * @param type $utc
     *
     * @return type
     */
    public static function usertimezone($utc)
    {
        $user = Auth::user();
        $tz = $user->timezone;
        $set = Settings::whereId('1')->first();
        $format = $set->dateformat;
        //$utc = date('M d Y h:i:s A');
        date_default_timezone_set($tz);
        $offset = date('Z', strtotime($utc));
        $date = date($format, strtotime($utc) + $offset);
        echo $date;
    }

    /**
     * Create a Article Template.
     *
     * @return type html
     */
    public function articleTemplateIndex()
    {
        try {
            return view('themes.default1.agent.kb.article.template.index');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * Info a Article Template.
     *
     * @param type Category $category
     *
     * @return type json
     */
    public function getArticleTemplateData(Request $request)
    {

        try {
            $pagination = ($request->input('limit')) ?: 10;

            $sortBy = ($request->input('sort')) ? $request->input('sort') : 'id';
            $search = $request->input('search');
            $orderBy = ($request->input('order')) ? $request->input('order') : 'desc';

            $baseQuery = ArticleTemplate::select('id', 'name', 'status')->orderBy($sortBy, $orderBy);
            $searchQuery = $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('status', 'LIKE', '%' . $search . '%');
            })
                ->paginate($pagination);

            return successResponse($searchQuery);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * Create a Article Template.
     *
     * @param type Category $category
     *
     * @return type html
     */
    public function createTemplate()
    {
        try {

            return view('themes.default1.agent.kb.article.template.create');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * This method store and update article template
     * @param ArticleTemplateRequest $request
     * @return type message
     */
    public function postTemplate(ArticleTemplateRequest $request)
    {
        try {
            ArticleTemplate::updateOrCreate(
                ['id' => $request->id], ['name' => $request->name, 'status' => $request->status, 'description' => $request->description]
            );
            $outputMessage = ($request->id) ? Lang::get('lang.articletemplate_updated_successfully') : Lang::get('lang.articletemplate_saved_successfully');
            return successResponse($outputMessage);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * this method return article template edit page
     * @return type html
     */
    public function editTemplate()
    {
        try {
            return view('themes.default1.agent.kb.article.template.edit');
        } catch (Exception $ex) {
            return redirect()->back()->with('fails', $ex->getMessage());
        }
    }

    /**
     * This method return article template info
     * @param type int $templateId
     * @return type json
     */
    public function editApiTemplate($templateId)
    {
        try {
            $articleTemplate = ArticleTemplate::where('id', $templateId)->first();

            return successResponse(['template' => $articleTemplate]);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * This method delete article template
     * @param  int $templateId
     * @return type message
     */
    public function postTemplateDelete($templateId)
    {
        try {
           
            $template = ArticleTemplate::findOrFail($templateId);
            $template->delete();
            return successResponse(Lang::get('lang.articletemplate_deleted_successfully'));
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

    /**
     * This method return article template info
     * @param type int $templateId
     * @return type json
     */
    public function getTemplateDetails($templateId)
    {
        try {
            $templates = ArticleTemplate::where('id', $templateId)->select('id', 'description')->first();
            return successResponse('', $templates);
        } catch (Exception $ex) {
            return errorResponse($ex->getMessage());
        }
    }

}
