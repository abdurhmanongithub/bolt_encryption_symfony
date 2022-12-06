<?php 

/**
 * src : source folder 
 * encrypted : Output folder
 */

$src      = '/home/bob/CODEHUB/encryption_test/source/src';
$php_blot_key = "kyc7fh";


/**
 * No need to edit following code 
 */

$excludes = array('Kernel.php');

foreach($excludes as $key => $file){
    $excludes[ $key ] = $src.'/'.$file;
}

$rec = new RecursiveIteratorIterator(new RecursiveDirectoryIterator( $src ),RecursiveIteratorIterator::SELF_FIRST
);
$require_funcs = array('include_once', 'include', 'require', 'require_once');


foreach ($rec as $file) {
    if ($file->isDir()) {
        $newDir  = str_replace( 'src', 'encrypted', $file );
        if( !is_dir( $newDir ) ) mkdir( $newDir );
        continue;
    };

    $filePath = $file->getPathname();

    if( pathinfo($filePath, PATHINFO_EXTENSION) != 'php'  ||
        in_array( $filePath, $excludes ) ) {  
        $newFile  = str_replace('src', 'encrypted', $filePath );
        copy( $filePath, $newFile );
        continue;
    }

    $contents = file_get_contents( $filePath );

    $pattern = '/\<\?php/m';
    preg_match($pattern, $contents, $matches);
    if (!empty($matches[0])) {
        $contents = preg_replace($pattern, '', $contents);
    }
    $cipher   = bolt_encrypt( $contents, $php_blot_key );
    $preppand = "<?php 
bolt_decrypt( __FILE__ , '$php_blot_key'); return 0;
##!!!##";
    $newFile  = str_replace('src', 'encrypted', $filePath );
    $fp = fopen( $newFile, 'w');
    // die($preppand . $cipher);
    fwrite($fp, $preppand.$cipher);
    
    fclose($fp);

    unset( $cipher );
    unset( $contents );
}

$out_str       = substr_replace($src, '', 0, 4);
$file_location = __DIR__."/encrypted/".$out_str;
echo "Successfully Encrypted... Please check in <b>" .$file_location."</a></b> folder.";




