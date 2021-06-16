<?php
namespace App\Http\Controllers\Admin\helpdesk;

// controllers
use App;
// requests
use App\Http\Controllers\Controller;
//supports
use App\Model\helpdesk\Settings\System;
use Config;
//classes
use File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Input;
use Lang;
use Validator;
use UnAuth;
use Auth;
use Cache;
use Illuminate\Http\Request;
use App\Model\helpdesk\Settings\Plugin;

/**
 * Handles all the language related operations like language change, getting languages etc.
 */
class LanguageController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return type void
     */
    public function __construct()
    {
        $this->middleware('auth',['except'=>'getLanguageFile']);
        $this->middleware('roles',['except'=>'getLanguageFile']);
    }

    /**
     * Switch language at runtime.
     *
     * @param type "" $lang
     *
     * @return type response
     */
    public function switchLanguage($lang)
    {
        $changed = UnAuth::changeLanguage($lang);
        if (!$changed) {
            return \Redirect::back()->with('fails', Lang::get('lang.language-error'));
        } else {
            return \Redirect::back();
        }
    }

    /**
     * Shows language page.
     *
     * @return type response
     */
    public function index()
    {
        return view('themes.default1.admin.helpdesk.language.index');
    }

    /**
     * Shows Language upload form.
     *
     * @return type response
     */
    public function getForm()
    {
        return view('themes.default1.admin.helpdesk.language.create');
    }

    /**
     * Provide language datatable to language page.
     *
     * @return type
     */
    public function getLanguages()
    {
        $path = base_path('resources/lang');
        $values = scandir($path);  //Extracts names of directories present in lang directory
        $values = array_slice($values, 2); // skips array element $value[0] = '.' & $value[1] = '..'
        return \DataTables::collection(new Collection($values))
                ->addColumn('language', function ($model) {

                    $img_src = assetLink('image','flag').'/'. $model . '.png';
                    if ($model == Config::get('app.fallback_locale')) {
                        return '<img src="' . asset($img_src) . '"/>&nbsp;' . Config::get('languages.' . $model)[0] . ' (' . Lang::get('lang.fallback_locale') . ')';
                    } else {
                        return '<img src="' . asset($img_src) . '"/>&nbsp;' . Config::get('languages.' . $model)[0];
                    }
                })
                ->addColumn('native-name', function ($model) {
                    return Config::get('languages.' . $model)[1];
                })
                ->addColumn('id', function ($model) {
                    return $model;
                })
                ->addColumn('status', function ($model) {
                    $system = System::select('content')->where('id', 1)->first();
                    $sys_lang = $system->content;
                    if ($sys_lang === $model) {
                        return "<span class='btn btn-xs btn-default' style='color:green;pointer-events:none;'>" . Lang::get('lang.yes') . '</span>';
                    } else {
                        return "<span class='btn btn-xs btn-default' style='color:red;pointer-events:none;'>" . Lang::get('lang.no') . '</span>';
                    }
                })
                ->addColumn('action', function ($model) {
                    $system = System::select('content')->where('id', 1)->first();
                    $sys_lang = $system->content;
                    if ($sys_lang === $model) {
                        return "<a href='change-language/" . $model . "'><input type='button' class='btn btn-primary btn-xs ' disabled value='" . Lang::get('lang.make-default') . "'/></a>&nbsp;&nbsp;
                <a href='change-language/" . $model . "' class='btn btn-primary btn-xs ' disabled><i class='fa fa-trash' style='color:white;'> &nbsp;</i> " . Lang::get('lang.delete') . '</a>';
                    } else {

                        $url = url('delete-language/' . $model);
                        $confirmation = deletePopUp($model, $url, "Delete", 'btn btn-primary btn-xs', 'Delete', true, 'delete');



                        return "<a href='change-language/" . $model . "'><input type='button' class='btn btn-primary btn-xs ' value='" . Lang::get('lang.make-default') . "'/></a> &nbsp;&nbsp;
                                      " . $confirmation;
                    }
                })
                ->rawColumns(['action','language','status'])
                ->make();
    }

    /**
     * handle language file uploading.
     *
     * @return response
     */
    public function postForm()
    {
        try {
            // getting all of the post data
            $file = [
                'File' => Input::file('File'),
                'language-name' => Input::input('language-name'),
                'iso-code' => Input::input('iso-code'),
            ];

            // setting up rules
            $rules = [
                'File' => 'required|mimes:zip|max:30000',
                'language-name' => 'required',
                'iso-code' => 'required',
            ]; // and for max size
            // doing the validation, passing post data, rules and the messages
            $validator = Validator::make($file, $rules);
            if ($validator->fails()) {

                // send back to the page with the input data and errors
                return Redirect::back()->withInput()->withErrors($validator);
            } else {

                //Checking if package already exists or not in lang folder
                $path = base_path('resources/lang');
                if (in_array(strtolower(Input::get('iso-code')), scandir($path))) {

                    //sending back with error message
                    Session::flash('fails', Lang::get('lang.package_exist'));
                    Session::flash('link', 'change-language/' . strtolower(Input::get('iso-code')));

                    return Redirect::back()->withInput();
                } elseif (!array_key_exists(strtolower(Input::get('iso-code')), Config::get('languages'))) {//Checking Valid ISO code form Languages.php
                    //sending back with error message
                    dd(Config::get('languages'), strtolower(Input::get('iso-code')));
                    Session::flash('fails', Lang::get('lang.iso-code-error'));

                    return Redirect::back()->withInput();
                } else {

                    // checking file is valid.
                    if (Input::file('File')->isValid()) {
                        $name = Input::file('File')->getClientOriginalName(); //uploaded file's original name
                        $destinationPath = base_path('public/uploads/'); // defining uploading path
                        $extractpath = base_path('resources/lang') . '/' . strtolower(Input::get('iso-code')); //defining extracting path
                        mkdir($extractpath); //creating directroy for extracting uploadd file
                        //mkdir($destinationPath);
                        Input::file('File')->move($destinationPath, $name); // uploading file to given path
                        \Zipper::make($destinationPath . '/' . $name)->extractTo($extractpath); //extracting file to give path
                        //check if Zip extract foldercontains any subfolder
                        $directories = File::directories($extractpath);
                        //$directories = glob($extractpath. '/*' , GLOB_ONLYDIR);
                        if (!empty($directories)) { //if extract folder contains subfolder
                            $success = File::deleteDirectory($extractpath); //remove extracted folder and it's subfolder from lang
                            //$success2 = File::delete($destinationPath.'/'.$name);
                            if ($success) {
                                //sending back with error message
                                Session::flash('fails', Lang::get('lang.zipp-error'));
                                Session::flash('link2', 'http://www.ladybirdweb.com/support/show/how-to-translate-faveo-into-multiple-languages');

                                return Redirect::back()->withInput();
                            }
                        } else {
                            // sending back with success message
                            Session::flash('success', Lang::get('lang.upload-success'));
                            Session::flash('link', 'change-language/' . strtolower(Input::get('iso-code')));

                            return Redirect::route('LanguageController');
                        }
                    } else {
                        // sending back with error message.
                        Session::flash('fails', Lang::get('lang.file-error'));

                        return Redirect::route('form');
                    }
                }
            }
        } catch (\Exception $e) {
            Session::flash('fails', $e->getMessage());
            Redirect::back()->withInput();
        }
    }

    /**
     * allow user to download language template file.
     *
     * @return type
     */
    public function download()
    {
        $path = 'downloads' . DIRECTORY_SEPARATOR . 'en.zip';
        $file_path = public_path($path);

        return response()->download($file_path);
    }

    /**
     * This function is used to delete languages.
     *
     * @param type $lang
     *
     * @return type response
     */
    public function deleteLanguage($lang)
    {
        if ($lang == Config::get('app.fallback_locale')) {
            Session::flash('fails', Lang::get('lang.lang-fallback-lang'));
            return redirect('languages');
        }
        $defaultLanguage = (System::select('content')->where('id', 1)->first()->content) ? System::select('content')->where('id', 1)->first()->content : "en";
        if ($lang == $defaultLanguage) {
            Session::flash('fails', Lang::get('lang.lang-default-lang'));
            return redirect('languages');
        }

        $deletePath = base_path('resources/lang') . '/' . $lang;     //define file path to delete
        $success = File::deleteDirectory($deletePath); //remove extracted folder and it's subfolder from lang
        if ($success) {
            //sending back with success message
            Session::flash('success', Lang::get('lang.delete-success'));

            return Redirect::back();
        }
        //sending back with error message
        Session::flash('fails', Lang::get('lang.lang-doesnot-exist'));

        return Redirect::back();
    }

    /**
     *
     * @param \App\Model\helpdesk\Utility\CountryCode $code
     * @param \Illuminate\Http\Request $request
     */
    public function getIsocode(Request $request)
    {
        try {
            $iso_code = $request->selected_lang;
            echo $iso_code;
        } catch (Exception $e) {

        }
    }


    /**
     * Gets language file content as array based on current language chosen
     * by the user (if not chosen by the user then language chosen by the admin will be fetched)
     * NOTE : currently we are caching the entire language file, but this has to change
     * @param \Illuminate\Http\Request $request
     * @return array                                language file as a single array
     */
    public function getLanguageFile()
    {
        $languages = array_unique([Lang::getFallback(), App::getLocale()]);
        
        $languageArray =[];
        
        foreach ($languages as $lang) {
           $this->appendCoreLanguage($lang, $languageArray);
           $this->appendPluginLanguage($lang, $languageArray);
           $this->appendModuleLanguage($lang, $languageArray);
        }
        
        header('Content-Type: text/javascript');
        // caching for 30 days
        header("Cache-Control: max-age=2592000");
        echo('translator = ' . json_encode($languageArray) . ';');
        exit();
    }

    /**
     * Fetches language array of given language for core Helpdesk and merges
     * it in $languageArray
     * 
     * @param   string  $languageName
     * @param   array   $languageArray
     * @param   array   $languageArray
     * @return  void
     */
    private function appendCoreLanguage(string $languageName, Array &$languageArray) :void
    {
        $path = resource_path('lang/' . $languageName);
        $this->updateLanguageArray($path, $languageArray);
    }

    /**
     * Fetches language array of given language for additional modules and merges
     * it in $languageArray
     *
     * @var     array   $moduleLangPaths  array containing path to lang directory in
     *                                    different modules
     * @param   string  $languageName
     * @param   array   $languageArray
     * @return  void
     */
    private function appendModuleLanguage(string $languageName, Array &$languageArray)
    {
        $moduleLangPaths = ['TimeTrack/resources/lang', 'Bill/lang', 'FaveoReport/lang', "FaveoLog/lang"];
        foreach ($moduleLangPaths as $value) {
            $path = app_path($value.DIRECTORY_SEPARATOR. $languageName);
            $this->updateLanguageArray($path, $languageArray);
        }
    }

    /**
     * Fetches language array of given language for active plugins and merges
     * it in $languageArray
     *
     * @param   string  $languageName
     * @param   array   $languageArray
     * @return  void
     */
    private function appendPluginLanguage(string $languageName, Array &$languageArray) :void
    {
        $activatePlugins = Plugin::where('status',1)->pluck('name');
        foreach ($activatePlugins as $plugin) {
            $path = app_path("Plugins/$plugin/lang/$languageName");
            $this->updateLanguageArray($path, $languageArray);
        }
    }

    /**
     * Returns an array of filenames with .php extension in given directory path
     *
     * @param   string  $path  path to directory from which .php files
     * @return  array          empty array if given path is not a directory otherwise
     *                         array containing app .php filenames with path
     */
    private function getLanguageFileArray(string $path) :array
    {
        if(!is_dir($path)) return [];

        return glob($path.DIRECTORY_SEPARATOR."*.php");
    }

    /**
     * Function which actually fetches language array data from all ".php" lanaguge
     * files availanle in the given path and merges that into $languageArray
     *
     * @param   string  $path
     * @param   array   $languageArray
     * @return  void
     */
    private function updateLanguageArray(string  $path, &$languageArray) :void
    {
        $files = $this->getLanguageFileArray($path);
        foreach ($files as $file) {
            $name           = basename($file, '.php');
            // merge lang files with same name
            if (array_key_exists($name, $languageArray)) {
                $languageArray[$name] = array_merge($languageArray[$name], require $file);
            } else {
                $languageArray[$name] = require $file;
            }
        }
    }
}
