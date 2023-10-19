<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MasterController extends Controller
{
    public function master(Request $request)
    {
        if ($request->method == 'list') {
            $data = DB::table('master')->where('type', '=', $request->type)->whereIn('status', $request->status)->get();
            return $data;
        }
        if ($request->method == 'prp-faceing') {

            $data  = DB::select('SELECT * from master where 
                type="'. $request->type.'"
                AND  id IN (SELECT faceing from unit_prices where project_id="'.$request->project_id.'")
                AND status IN ("'. implode(',', $request->status).'") 
            ');
            // $data = DB::table('master')
            // ->where('type', '=', $request->type)
            // ->whereIn('id', [DB::table('unit_prices')->select('faceing')->where('project_id', '=', $request->project_id)->get()])
            // ->whereIn('status', $request->status)->get();
            return $data;
        }
        if ($request->method == 'cat_based') {
            $data = DB::table('master')->where('type', '=', $request->type)->where('cat', '=', $request->cat_id)->whereIn('status', $request->status)->get();
            return $data;
        }
        if ($request->get('method') == 'create') {
            do {
                $id = '';
                $keys = array_merge(range(0, 9), range('A', 'Z'));

                for ($i = 0; $i < 10; $i++) {
                    $id .= $keys[array_rand($keys)];
                }

            } while (DB::table('master')->where("id", "=", $id)->first());

            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $picture = $id . '-' . date('dmYHis') . '.' . $file->getClientOriginalExtension();
            $isupload = $file->storeAs('uploads', $picture);
            if ($isupload) {
                $values = array(
                    'id' => $id,
                    'type' => $request->get('type'),
                    'cat' => $request->get('cat'),
                    'title' => $request->get('title'),
                    'status' => $request->get('status'),
                    'image' => $picture,
                );
                $data = DB::table('master')->insert($values);
                if ($data) {
                    return ["message" => "success"];
                } else {
                    return ["message" => "faild"];
                }
            } else {
                return ["error" => "Something wrng"];
            }

        }

    }
    public function link(Request $request)
    {
        if ($request->get('method') == 'create') {
            // return $request->get('linkid');
            if ($request->get('linkid') == 'null') {
                do {
                    $link_id = Str::random(10);
                } while (DB::table('links')->where("id", "=", $link_id)->first());
                $values['id'] = $link_id;
            }
            $file = $request->file('file');

            if (($request->get('linkid') != null) && ($file != null)) {
          
                $filename = DB::table('links')->where('id', '=', $request->get('linkid'))->first();
                if (@$filename->image != null) {
                    if (File::exists(storage_path('app/icons/' . $filename->image))) {
                        unlink(storage_path('app/icons/' . $filename->image));
                    }
                }
            }

            if ($file != null) {
                $filename = $file->getClientOriginalName();
                $picture = 'menu-' . date('dmYHis') . '.' . $file->getClientOriginalExtension();
                $isupload = $file->storeAs('icons', $picture);
                $values['image'] = $picture;
            } else {
                
                $picture = null;
            }

            $values['link_type'] = $request->get('menutype');
            if ($request->get('menutype') == 1) {
                $values['link_name'] = $request->get('menuname');
            }

            if ($request->get('menutype') == 2) {
                $values['link_id'] = $request->get('menuname');
                $values['link_name'] = $request->get('submenu');
            }

            $values['path'] = $request->get('path');
            $values['orderby'] = $request->get('order');
            $values['status'] = $request->get('status');

            if ($request->get('linkid') == 'null') {
                $data = DB::table('links')->insert($values);
            } else {
               
                $data = DB::table('links')->where('id', '=', $request->get('linkid'))->update($values);
            }

            if ($data) {
                return ["message" => "success"];
            } else {
                return ["message" => "faild"];
            }
        }
        if ($request->get('method') == 'get') {

            $menu = DB::select('SELECT * from links where link_type=1 AND  status IN ("active", "inactive") order by orderby ASC');
            $data = array();
            foreach ($menu as $f => $mainmenu) {
                $data[$f]['id'] = $mainmenu->id;
                $data[$f]['link_name'] = $mainmenu->link_name;
                $data[$f]['path'] = $mainmenu->path;
                $data[$f]['image'] = $mainmenu->image;
                $data[$f]['orderby'] = $mainmenu->orderby;
                $data[$f]['submenu'] = DB::select('SELECT * from links where link_id ="' . @$mainmenu->id . '" AND  status IN ("active", "inactive")  order by orderby ASC');
                // $data[$f]['submenu']=$submenu ;
            }
            return $data;

        }
        if ($request->get('method') == 'link_type') {

            $data = DB::table('links')->where('link_type', '=', $request->menutype)->whereIn('status', $request->status)->get();
            return $data;
        }
        if ($request->get('method') == 'details') {

            $data = DB::table('links')->where('id', '=', $request->linkid)->first();
            return $data;
        }
        if ($request->get('method') == 'getlinkmapping') {
            $menu = DB::select('SELECT l.id, l.link_name, l.path, l.image, IF( m.title IS NULL, "false", "true") as checked from links l LEFT JOIN master m ON m.title=l.id and m.cat="' . $request->type . '" and  m.rel_id="' . $request->user_type . '" where l.link_type=1 AND  l.status IN ("active", "inactive")');
            $data = array();
            foreach ($menu as $f => $mainmenu) {
                $data[$f]['id'] = $mainmenu->id;
                $data[$f]['link_name'] = $mainmenu->link_name;
                $data[$f]['path'] = $mainmenu->path;
                $data[$f]['image'] = $mainmenu->image;
                $data[$f]['checked'] = $mainmenu->checked;
                $data[$f]['submenu'] = DB::select('SELECT l.id, l.link_name, l.path, l.image, IF( m.title IS NULL, "false", "true") as checked from links l  LEFT JOIN master m ON m.title=l.id and m.cat="' . $request->type . '" and  m.rel_id="' . $request->user_type . '" where l.link_id ="' . @$mainmenu->id . '" AND  l.status IN ("active", "inactive")');
                // $data[$f]['submenu']=$submenu ;
            }
            return $data;
        }
        if ($request->get('method') == 'linkpermision') {
            DB::table('master')->where('type', 'linkmapping')->where('cat', $request->type)->where('rel_id', $request->user_type)->delete();
            for ($i = 0; $i <= count($request->linkid) - 1; $i++) {
                do {
                    $link_per_id = Str::random(10);
                } while (DB::table('master')->where("id", "=", $link_per_id)->first());
                $link_per['id'] = $link_per_id;
                $link_per['type'] = 'linkmapping';
                $link_per['cat'] = $request->type;
                $link_per['rel_id'] = $request->user_type;
                $link_per['title'] = $request->linkid[$i];

                DB::table('master')->insert($link_per);
            }
        }
    }
    public function whitelabel(Request $request)
    {
        if ($request->method == "update") {

            $values['business_id'] = $request->bisiness_id;
            $values['url'] = $request->url;
            $values['bgcolor'] = $request->bgcolor;
            $values['color'] = $request->color;
            $values['status'] = $request->status;

            $checkwhitelabel = DB::table('white_label')->where("business_id", "=", $request->bisiness_id)->get();
            
            if (count($checkwhitelabel)==0) {
                do {
                    $white_label_id = Str::random(10);
                } while (DB::table('white_label')->where("id", "=", $white_label_id)->first());
                $values['id'] = $white_label_id;

                $data = DB::table('white_label')->insert($values);
                if ($data) {
                    return ["message" => "success"];
                } else {
                    return ["message" => "faild"];
                }
               
            } else {
                $data = DB::table('white_label')->where('business_id', '=', $request->business_id)->update($values);
                if ($data) {
                    return ["message" => "success"];
                } else {
                    return ["message" => "faild"];
                }
            }

            

        }
        if ($request->method == "get") {
            
            $data = DB::table('white_label')->where("business_id", "=", $request->bisiness_id)->get();

            return $data;
        }
        if ($request->method == "geturl") {
            if(($request->url=='localhost') || ($request->url=='realestate.holaciti.com')){
                $data = DB::table('white_label as wh')
                ->selectRaw('wh.bgcolor, wh.color, wh.business_id as logo, wh.business_id ')
                
                ->where("wh.url", "=", $request->url)->first();
    
            }else{
                $data = DB::table('white_label as wh')
                ->selectRaw('wh.bgcolor, wh.color, b.logo, wh.business_id ')
                ->leftjoin('business as b', 'b.id', '=', 'wh.business_id')
                ->where("wh.url", "=", $request->url)->first();
    
            }
           
            return $data;
        }

    }
}
