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
                $element['profilePicture'] =  'Image not found';
        } else{
            $file = file_get_contents($userPath);
            $element['profilePicture'] = base64_encode($file);

        }
        }
 }
return $array;
}

function generateRandomString() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    
    // Ensure at least one number, uppercase letter, and lowercase letter
    $randomString .= rand(0, 9); // at least one number
    $randomString .= $characters[rand(10, 35)]; // at least one uppercase letter
    $randomString .= $characters[rand(36, 61)]; // at least one lowercase letter
    
    // Fill the rest of the string with random characters
    for ($i = 0; $i < 5; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    
    // Shuffle the string to ensure randomness
    $randomString = str_shuffle($randomString);
    
    return $randomString;
  }