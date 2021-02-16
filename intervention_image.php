<?php
/***********************************************
Intervention Image package setup--"http://image.intervention.io/getting_started/installation"
***********************************************/

//==============================================================================
//CMD--this command will download "Intervention image package" in vendor

composer require intervention/image           //only package will download---(its best)
//or
php composer.phar require intervention/image  //download with composer---(sometimes no work it properly)

//==============================================================================
//go--"config/app.php"

//In the "$providers" array add the service providers for this package.
Intervention\Image\ImageServiceProvider::class,

//Add the facade of this package to the "$aliases" array.
'Image' => Intervention\Image\Facades\Image::class,

//==============================================================================
//CMD--(publish Configuration)
//it will set "image.php" named file in "config folder"
php artisan vendor:publish --provider="Intervention\Image\ImageServiceProviderLaravelRecent"

//==============================================================================
//image insert code Example...
//Controller (here BannerController, Model-Banner)

use App\Models\Banner;
use Image;
use Carbon\Carbon;
use Session;

//--------------------------------------
// Upload Image
//--------------------------------------
public function add_banner_post(Request $request)
{
    $info = Banner::create($request->except("_token"));   //insert all Banner info

    // Image Upload code
    if ($request->hasFile("banner_image")) {

        $photo = $request->file('banner_image');
        $extension = $photo->getClientOriginalExtension();
        $photo_new_name = "banner_".$info->id.".".$extension;
        $photo_save_path = public_path("storage/banner/".$photo_new_name);

        Image::make($photo)->resize(870, 370)->save($photo_save_path);   // Intervention Image upload package

        Banner::findOrFail($info->id)->update([
            "banner_image"=>$photo_new_name,
            "updated_at"=>Carbon::now(),
        ]);
    };

    if ($info) {
        Session::flash("success", "Successfully Inserted Banner Information !");
        return redirect("all_banner");
    } else {
        Session::flash("unsuccess", "Banner Insert Failed !");
        return back();
    };
}


//-------------------------------------
// Update/Edit Image
//-------------------------------------
public function edit_banner_post(Request $request)
{
    $request->validate([
        "title"=>"required|string|min:1|max:100",
    ]);
    $update = Banner::find($request->banner_id)->update([
        "title"=>$request->title,
        "updated_at"=>Carbon::now(),
    ]);

    // Image Update code
    if ($request->hasFile("banner_image")) {
        $photo = $request->file('banner_image');
        $extension = $photo->getClientOriginalExtension();
        $photo_new_name = "banner_".$request->banner_id.".".$extension;
        $photo_save_path = public_path("storage/banner/".$photo_new_name);

        $before_image = Banner::find($request->banner_id)->banner_image;
        if ($before_image != "") {
            unlink(public_path("storage/banner/".$before_image));
        };

        Image::make($photo)->resize(870, 370)->save($photo_save_path);  // Intervention Image upload package

        Banner::find($request->banner_id)->update([
            "banner_image"=>$photo_new_name,
            "updated_at"=>Carbon::now(),
        ]);
    };

    if ($update) {
        Session::flash("success", "Successfully Update Banner Information !");
        return redirect("banner_details/".$request->banner_id);
    } else {
        Session::flash("unsuccess", "Banner Insert Failed !");
        return back();
    };
}

//==============================================================================
//Usefull built in function for file
getClientOriginalExtension()----//get Extension
getClientOriginalName()---------//get Name
getSize()-----------------------//get Size
getClientMimeType()-------------//get MimeType likeimage/jpg
getimagesize($photo)[0]---------//get Width
getimagesize($photo)[1]---------//get Height

// Intervention Image functions for custom controll...
make()
save()
resize()
height()
heighten()
width()
widen()
//for more details---"http://image.intervention.io/getting_started/installation"

//=============================================================================
//=== END ===//
