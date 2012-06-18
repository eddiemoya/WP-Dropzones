<?php

/**
 * Simple methods to quickly handle routine tedious, and otherwise error-prone tasks.
 *
 * @author Eddie Moya
 * @
 */
Abstract class WPDZ_Helper {
    
    
    /**
     * Include view.
     * 
     * @author Eddie Moya
     * @uses WPDZ_Helper::include_file();
     * 
     * @param string $filename [required] Filename to be included, no leading or trailing slashes are needed.
     * @param string $relpath [optional] Path to file, relative to views folder
     * 
     * @return void
     */
    public function include_view($filename, $relpath = '', $return_path = false) {
        
        if($return_path){
            return self::include_file($filename, self::determine_relpath('views', $relpath), $return_path);
            
        } else {
        
            self::include_file($filename, self::determine_relpath('views', $relpath));
        }
    }
    
    /**
     * Include controller.
     * 
     * @author Eddie Moya
     * @uses WPDZ_Helper::include_file();
     * 
     * @param string $filename [required] Filename to be included, no leading or trailing slashes are needed.
     * @param string $relpath [optional] Path to file, relative to classes folder
     * 
     * @return void
     */
    public function include_singleton($filename, $relpath = '', $return_path = false) {
        
        if($return_path){
            return self::include_file($filename, self::determine_relpath('singletons', $relpath), $return_path);
        }
        
        self::include_file($filename, self::determine_relpath('singletons', $relpath));
    }
    
    /**
     * Include controller.
     * 
     * @author Eddie Moya
     * @uses WPDZ_Helper::include_file();
     * 
     * @param string $filename [required] Filename to be included, no leading or trailing slashes are needed.
     * @param string $relpath [optional] Path to file, relative to classes folder
     * 
     * @return void
     */
    public function include_controller($filename, $relpath = '', $return_path = false) {
        
        if($return_path){
            return self::include_file($filename, self::determine_relpath('controllers', $relpath), $return_path );
        }
        
        self::include_file($filename, self::determine_relpath('controllers', $relpath, $return_path));
    }
    
    /**
     * Include models.
     * 
     * @author Eddie Moya
     * @uses WPDZ_Helper::include_file();
     * 
     * @param string $filename [required] Filename to be included, no leading or trailing slashes are needed.
     * @param string $relpath [optional] Path to file, relative to classes folder
     * 
     * @return void
     */
    public function include_model($filename, $relpath = '', $return_path = false) {
        
        if($return_path){
            return self::include_file($filename, self::determine_relpath('models', $relpath), $return_path);
        }
        
        self::include_file($filename, self::determine_relpath('models', $relpath, $return_path));
    }

    /**
     * Include asset.
     * 
     * @author Eddie Moya
     * @uses WPDZ_Helper::include_file();
     * 
     * @param string $filename [required] Filename to be included, no leading or trailing slashes are needed.
     * @param string $relpath [REQUIRED] Path to file, relative to assets folder  
     * 
     * @return void
     */
    public function include_asset($filename, $relpath, $return_path = false) {

            return self::include_file($filename, self::determine_relpath('assets', $relpath), $return_path);
  
    }

    /**
     * Include file.
     * 
     * @author Eddie Moya
     * @param string $filename [required] Filename to be included, no leading or trailing slashes are needed.
     * @param string $relpath [optional] Path to file, relative to root plugin folder folder.
     * 
     * @return void
     */
    public function include_file($filename, $relpath = '', $return_path = FALSE) {
        $has_extention = strchr($filename, '.');
        if(empty($has_extention)){
            $filename .= '.php';
        }
        
        $relpath = self::determine_relpath('', $relpath);
        $path = WPDZ_PATH . $relpath . $filename;
 
        if($return_path){
            return $path;
        } else {
        //if(file_exists($path))
       include ($path);
        }
    }
    
    /**
     * Determines filepath relative to a given base.
     * 
     * @author Eddie Moya
     * @param string $base Path relative to plugin root folder
     * @param string $relpath Path relative to $base.
     * 
     * @return string Full relative path. 
     */
    public function determine_relpath($base, $relpath){
        
        /**
         * If there is a relpath, but its last 
         * character is not a slash, add one.
         * but only if base is not empty.
         */
        if (!empty($relpath) && substr($relpath, -1) != "/") {
            $relpath.='/';
            if (!empty($base)  && substr($base, -1) != "/") {
                $base.= '/';
            }
        }
        
        return $base . $relpath;

    }

}