<?php
/**
 * Created by PhpStorm.
 * User: alcides
 * Date: 4/10/2018
 * Time: 7:44 p.m.
 */

namespace App\Utils;


class Util
{

    static function decodeBody()
    {
        return json_decode( trim( file_get_contents( "php://input" ) ), true );
    }

    static function filter_by_ids( $array, $id = null )
    {
        $result = [];
        foreach ( $array as $value )
            if ( is_object( $value ) )
                $result[] = $value->id ?? $value->getId(); else if ( is_array( $value ) && isset( $value[ 'id' ] ) ) {
                if ( $id )
                    return $value;
                $result[] = $value[ 'id' ];
            }

        return $result;
    }

    static function array_string_values( $array = [], $delimiter = ',' )
    {
        $result = "";
        for ( $i = 0; $i < count( $array ); $i++ ) {
            if ( is_object( $array[ $i ] ) && method_exists( $array[ $i ], 'getId' ) )
                $result .= $array[ $i ]->getId(); else
                $result .= $array[ $i ];
            if ( $i + 1 < count( $array ) )
                $result .= "$delimiter ";
        }
        return $result;
    }

    static function array_string_values_nospace( $array = [], $delimiter = ',' )
    {
        $result = "";
        for ( $i = 0; $i < count( $array ); $i++ ) {
            if ( is_object( $array[ $i ] ) && method_exists( $array[ $i ], 'getId' ) )
                $result .= $array[ $i ]->getId(); else
                $result .= $array[ $i ];
            if ( $i + 1 < count( $array ) )
                $result .= "$delimiter";
        }
        return $result;
    }

    static function notificationOrder( $notifications = [] )
    {
        $result = [];
        for($i = 0; $i < count($notifications); $i++){
            $key = null;
            $value = null;
            foreach ( $notifications as $key2 => $value2 ) {
                if(!$key){
                    $value = $value2;
                    $key = $key2;
                    continue;
                }
                if ( $value2[ 'order' ] >= $value[ 'order' ] ) {
                    $value = $value2;
                    $key = $key2;
                }
            }
            if($key){
                $result[$key] = $value;
                unset($notifications[$key]);
                $i--;
                if(empty($notifications))break;
            }
        }
        return $result;
    }

    static function tourOrder( $tours = [] )
    {
        $result = [];
        for($i = 0; $i < count($tours); $i++){
            $value = null;
            $cont = 0;
            foreach ( $tours as $key=>$tour ) {

                if(!$value){
                    $value = $tour;
                    $cont = $key;
                    continue;
                }
                if ( $tour->getStartDate() <= $value->getStartDate()) {
                    $value = $tour;
                    $cont = $key;
                }
            }
            if($value){
                $result[] = $value;
                unset($tours[$cont]);
                $i--;
            }
        }
        return $result;
    }
}