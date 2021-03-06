<?php

namespace App\Http\Controllers\Admin\Categories;

use App\Helper\funcHelper;
use App\Helper\notificationHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\categoryRequest;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;

class indexController extends Controller
{

    public function index()
    {
        $data = Categories::all();
        return view('admin.category.index', ['data'=>$data]);
    }

    public function create()
    {
    return view('admin.category.create');
    }

    public function store(categoryRequest $request)
    {
        $all = $request->except('_token');
        $all["permalink"] = funcHelper::permalink($request["name"]);
        $type = $all['type'];
        $control = Categories::where('permalink','=',$all['permalink'])->count();

        if ($type == 0 and $control == 0)
        {
                $insert = Categories::create($all);
                if ($insert)
                {
                    return notificationHelper::sendNotification('success','create');
                }
                else
                {
                    return notificationHelper::sendNotification('error');
                }

        }
        else if ($type == 0 and  $control >0)
        {

            return notificationHelper::sendNotification('error','saveddata');
        }
        else if ($type == 1 and $control == 0)
        {
            $insert = Categories::create($all);
            if ($insert)
            {
                return notificationHelper::sendNotification('success','create');
            }
            else
            {
                return notificationHelper::sendNotification('error');
            }
        }
        else if ($type == 1 and $control >0)
        {
            $mainCategory = Categories::select('permalink')->where('id','=',$all['mainCategoryID'])->get();
            $newlink = $mainCategory[0]['permalink'].'-'.$all['permalink'];
            $newcontrol = Categories::where('permalink','=',$newlink)->count();

            if ($newcontrol == 0)
            {
                $all['permalink'] = $newlink;
                $insert = Categories::create($all);
                if ($insert)
                {
                    return notificationHelper::sendNotification('success','create');
                }
                else
                {
                    return notificationHelper::sendNotification('error');
                }
            }
            else
                {
                    return notificationHelper::sendNotification('error','saveddata');
                }
        }

    }

    public function edit($id)
    {
        $category = Categories::where('id','=',$id)->get();
        return view('admin.category.edit',['data'=>$category]);
    }

    public function update(categoryRequest $request)
    {
        $id  = $request->route('id');
        $all = $request->except('_token');
        $all['permalink'] = funcHelper::permalink($request['name']);

        $update = Categories::where('id','=',$id)->update($all);

        if ($update)
        {
            return notificationHelper::sendNotification('success','update');
        }
        else
        {
            return notificationHelper::sendNotification('error');
        }

    }

    public function delete($id)
    {
        $delete = Categories::where('id','=',$id)->delete();
        if ($delete)
        {
            return notificationHelper::sendNotification('success','delete');
        }
        else
        {
            return notificationHelper::sendNotification('error');
        }
    }

    public function getMainCategory(Request $request)
    {
        $data = Categories::where('type','=',0)->get();
        $json = json_encode($data);
        return $json;
    }
}
