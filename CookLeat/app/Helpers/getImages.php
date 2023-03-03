<?php

function getImages($array){
    foreach ($array as $element) { 
        $recipeRoute = $element['image'];
        if (isset($recipeRoute)){
        $recipePath = storage_path('app/' . $recipeRoute);
        if (!file_exists($recipePath)) {
            return response()->json(['message' => 'Image not found'], 404);
        } else{
            $file = file_get_contents($recipePath);
            $element['image'] = base64_encode($file);
        }
    }
        $userRoute = $element['profilePicture'];
        if (isset($userRoute)){
            $userPath = storage_path('app/' . $userRoute);
        if (!file_exists($userPath)) {
            return response()->json([
                'message' => 'Image not found'
            ], 404);
        } else{
            $file = file_get_contents($userPath);
            $element['profilePicture'] = base64_encode($file);

        }
        }
 }
return $array;
}