<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\CssSelector\Node\FunctionNode;
use App\Models\Comments;

class BookController extends Controller
{
    //

    //fetch all book function
    public function fetch_books_endpoint(){


        $curl = curl_init();
    
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://www.anapioficeandfire.com/api/books",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    
        $result = "cURL Error #:" . $err;
    
        $data = json_decode($response);
    
        if ($err) {
            return response_data(false, 422, $data, ['errors' => json_decode($result)], false, false);
          
        } else {
    
         $empty_array = array();
         $empty_array_2 = array();
         foreach($data as $value){
              $data_2['url'] = $value->url;
              $data_2['name'] = $value->name;
              $data_2['authors'] = implode(",", $value->authors);
              $data_2['number_of_pages'] = $value->numberOfPages;
              $data_2['publisher'] = $value->publisher;
              $data_2['country'] = $value->country;
              $data_2['number_of_characters'] = count($value->characters);
              $data_2['characters_info'] = self::get_book_characters($value->url);
              $data_2['number_of_comments'] = count(Comments::where('book_name', $value->name)->get());

              //$empty_array['url'] = $value['url'];
          
            array_push($empty_array, $data_2);  
      }

      return response_data(true, 200, 'Books fetched.', ['values' => $empty_array], false, false);
        
    
        }
    
      }

    //get single book info when this function is called
    public function fetch_single_book($book){
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://www.anapioficeandfire.com/api/books",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    
        $result = "cURL Error #:" . $err;
    
        $data = json_decode($response);
    
        if ($err) {//check if there's an error response
            return response_data(false, 422, $data, ['errors' => json_decode($result)], false, false);
          
        } else {
    
         $empty_array = array();
         //$empty_array_2 = array();
         foreach($data as $value){
            if(str_contains($value->name, $book) || str_contains($value->url, $book)){
              $data_2['url'] = $value->url;
              $data_2['name'] = $value->name;
              $data_2['authors'] = implode(",", $value->authors);
              $data_2['number_of_pages'] = $value->numberOfPages;
              $data_2['publisher'] = $value->publisher;
              $data_2['country'] = $value->country;
              $data_2['characters_info'] = self::get_book_characters($value->url);
              $data_2['comments'] = Comments::where('book_name', $value->name)->get();

              //$empty_array['url'] = $value['url'];
          //push to arra
            array_push($empty_array, $data_2);  
            }
      }

      return response_data(true, 200, 'Book fetched.', ['values' => $empty_array], false, false);
        
    
        }
    }


     


     public function sort_character_name($char_name){
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://www.anapioficeandfire.com/api/characters",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    
        $result = "cURL Error #:" . $err;
    
        $data = json_decode($response);
    
        if ($err) {//check if there's an error response
          
            return response_data(false, 422, $data, ['errors' => json_decode($result)], false, false);
          
        } else {//else loop through the response array data
    
         $empty_array = array();
         //$empty_array_2 = array();
         foreach($data as $value){
          if(str_contains($value->name, $char_name) || str_contains(implode(",", $value->playedBy), $char_name)){
            $data_2['name'] = $value->name;
            $data_2['gender'] = $value->gender;
            $data_2['born'] = $value->born;
            $data_2['died'] = $value->died;
            $data_2['played_by'] = $value->playedBy;
            
            $data_2['books'] = $value->books;

            //$empty_array['url'] = $value['url'];
            //push to array data
            array_push($empty_array, $data_2);  
          }
         }

              
      
      //return array data
      return response_data(true, 200, 'Character data fetched.', ['values' => $empty_array], false, false);

    }

      
     }


     public function sort_character_gender($gender){

        $curl = curl_init();
    
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://www.anapioficeandfire.com/api/characters",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    
        $result = "cURL Error #:" . $err;
    
        $data = json_decode($response);
    
        if ($err) {//check if there's an error response
            
            return response_data(false, 422, $data, ['errors' => json_decode($result)], false, false);
          
        } else {//else loop through the response array data
    
         $empty_array = array();
         //$empty_array_2 = array();
         foreach($data as $value){
          if(str_contains($value->gender, $gender)){

            $data_2['name'] = $value->name;
            $data_2['gender'] = $value->gender;
            $data_2['born'] = $value->born;
            $data_2['died'] = $value->died;
            $data_2['played_by'] = $value->playedBy;
            
            $data_2['books'] = $value->books;
         

              //$empty_array['url'] = $value['url'];
            //push to array data
            array_push($empty_array, $data_2);  
          }
           }
       //return array data
      return response_data(true, 200, 'Character data fetched.', ['values' => $empty_array], false, false);

    }


    }


    public static function fetch_book_names_only(){
      $curl = curl_init();
    
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://www.anapioficeandfire.com/api/books",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    
        $result = "cURL Error #:" . $err;
    
        $data = json_decode($response);
    
        if ($err) {
            return response_data(false, 422, $data, ['errors' => json_decode($result)], false, false);
          
        } else {
    
         $empty_array = array();
         $empty_array_2 = array();
         foreach($data as $value){
              $data_2['name'] = $value->name;
              //$empty_array['url'] = $value['url'];
            array_push($empty_array, $data_2);  
      }
        return $empty_array;
      //return response_data(true, 200, 'Books fetched.', ['values' => $empty_array], false, false);
        
    
        }
    }


     //fetch book characters by parsing book url as an arguement
      public static function get_book_characters($book_url){

        $curl = curl_init();
    
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://www.anapioficeandfire.com/api/characters",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
        ));
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    
        $result = "cURL Error #:" . $err;
    
        $data = json_decode($response);
    
        if ($err) {//check if there's an error response
            return $result;
            //return response_data(false, 422, $data->message, ['errors' => json_decode($result)], false, false);
          
        } else {//else loop through the response array data
    
         $empty_array = array();
         //$empty_array_2 = array();
         
         foreach($data as $value){
             //check to see if there's a value to match the method arguement
             if(str_contains(implode(",", $value->books), $book_url)){
            $data_2['name'] = $value->name;
            $data_2['gender'] = $value->gender;
            $data_2['born'] = $value->born;
            $data_2['died'] = $value->died;
            $data_2['played_by'] = $value->playedBy;
            

            //$empty_array['url'] = $value['url'];
            //push to array data
            array_push($empty_array, $data_2);  
            }
            
         }

              
      
        return $empty_array;//return array data
      //return response_data(true, 200, 'Character data fetched.', ['values' => $empty_array], false, false);

    }

      }

}
