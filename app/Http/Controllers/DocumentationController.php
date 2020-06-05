<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Middleware\Backend;
use Illuminate\Support\Facades\App;

class DocumentationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index0(Request $request)
    {
       $topic = '';
       return $this->index ($request, $topic);
    }

    public function index(Request $request, $topic)
    {
        $backend = new Backend();
        $backend->timeout = 5;

        if ($topic == '') {
          $procname = 'workplace/GetDesktopHelpIdStart';
          $params = array ();
          $Article = $backend->call ($procname, $params);
        }
        else {
          $procname = 'workplace/GetDesktopHelpId';
          $params = array ('url' => $topic);
          $Article = $backend->call ($procname, $params);
        }
        $idArticle = (isset($Article)) ? $Article->idArticle : -1;


        $inp = ($request->input('searchText') !== null) ? $request->input('searchText') : '' ;
        $procname = 'workplace/GetDesktopHelpTreeBranch';
        if ($topic == '' and $inp !== '') {
          $params = array ('language' => App::getLocale(), 'idRow' => -1, 'search' => $inp );
        }
        else {
          $params = array ('language' => App::getLocale(), 'id' => $idArticle, 'idRow' => $idArticle, 'idValue' => $idArticle, 'search' => $inp );
        }
        if ($backend->error == '') {
          $Tree = $backend->call ($procname, $params);
          if (isset($Tree->idSelect)) { 
            $idArticle = $Tree->idSelect;
          }
        }
        if ($backend->error == '') {
          $procname = 'workplace/GetDesktopHelpText';
          $params = array ('language' => App::getLocale(), 'idArticle' => $idArticle, 'search' => $inp );
          $Text = $backend->call ($procname, $params);

          $doctext = $Text->text;

          // Highlight search text 
          $replace = "<span style='background-color:yellow;'>" . $inp . "</span>";
//          $doctext = str_ireplace ( $inp , $replace , $doctext ) ;
          if ($inp !== '') {
            $doctext = mb_eregi_replace($inp, $replace, $doctext);
          }
        }

        if ($backend->error == '') {
          $treeMenu = '';
          $title = '';
          $titleFull = '';
          if(isset($Tree->data) && is_array($Tree->data)) {
            $treeMenu = $this->RenderTree ($Tree->data, $idArticle, $inp, false); 
            $title = $this->FindTitle ($Tree->data, $idArticle, false); 
            $titleFull = $this->FindFullTitle ($Tree->data, $idArticle, false); 
          }
          $prevurl = '';
          if(isset($Tree->prevurl) and $Tree->prevurl !== '') {
            $prevurl = '/documentation/' . $Tree->prevurl;
          }
          $nexturl = '';
          if(isset($Tree->nexturl) and $Tree->nexturl !== '') {
            $nexturl = '/documentation/' . $Tree->nexturl;
          }
          return view('documentation', ['tree' => $treeMenu, 'text' => $doctext, 'prevUrl' => $prevurl, 
                                        'nextUrl' => $nexturl, 'title' => $title, 'titleFull' => $titleFull, 'search' => $inp] );
        }
        else {
          return view('documentation', ['tree' => '', 'text' => $backend->error, 'prevUrl' => '', 
                      'nextUrl' => '', 'title' => 'Error', 'titleFull' => 'Error', 'search' => $inp]);
        }
    }
    public function RenderTree ($branchArray, $idSel, $searchText='', $submenuFlag=false)
    {
	$output = '';
	foreach($branchArray as $k => $item)
        {
		if(isset($item->url) && isset($item->id))
		{
			$itemCss = ($submenuFlag) ? 'item item-sub-menu' : 'item';
			$itemUrl =  "/documentation/" . $item->url;
                        if ($searchText !== '') {
                          $itemUrl .= '?searchText=' . $searchText;
                        }
			if($item->id == $idSel){
				$itemCss .= ' active-item';
			}
			$output .= '<li class="' . $itemCss . '">';
	
			if(isset($item->children) && is_array($item->children) && count($item->children)){
				$output .= '<span class="help_branch_list_icon">&#9660;</span>&nbsp;<a href="' . $itemUrl . '">' . $item->text . '</a>';
				$output .= '<ul class="sub-menu">';
				$output .= $this->RenderTree($item->children, $idSel, $searchText, true);
				$output .= '</ul>';
			} else {
				if(isset($item->state) && $item->state == 'closed'){
					$output .= '<a href="' . $itemUrl . '"><span class="help_branch_list_icon">&#9658;</span>&nbsp;' . $item->text . '</a>';
				} else {
					$output .= '<a href="' . $itemUrl . '">' . $item->text . '</a>';
				}
			}
			$output .= '</li>';
		}
	}
	return $output;
    }
    public function FindTitle ($branchArray, $idSel, $submenuFlag=false)
    {
	$output = '';
	foreach($branchArray as $k => $item)
        {
		if(isset($item->url) && isset($item->id))
		{
			if($item->id == $idSel){
				$output = $item->text;
				return $output;
			}
			if(isset($item->children) && is_array($item->children) && count($item->children)){
				$output = $this->FindTitle($item->children, $idSel, true);
			}
	        }
	}
	return $output;
    }
    public function FindFullTitle ($branchArray, $idSel, $submenuFlag=false)
    {
	$output = '';
	foreach($branchArray as $k => $item)
        {
		if(isset($item->url) && isset($item->id))
		{
			if($item->id == $idSel){
				$output = $item->text;
				return $output;
			}
			if(isset($item->children) && is_array($item->children) && count($item->children)){
				$output = $this->FindFullTitle($item->children, $idSel, true);
				if ($output !== '') {
					return $item->text . ' -> ' . $output;
				}
			}
	        }
	}
	return $output;
    }
}
