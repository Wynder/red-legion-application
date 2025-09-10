<?php
/* Functions
 * This file contains various functions used throughout the application.
 * @author : Tealstone

/** 
 * d()
 *
 * Show debug information only to people in the $_DEBUG_USERS array.
 */
function d($display)
{

        if(true)
        {
                echo '<br><font color="red">**START**</font> <pre>';

                if(is_array($display))
                {
                        print_r($display);
                }
                elseif(is_object($display))
                {
                        $d = @get_object_vars($display);
                        if(is_array($d))
                        {
                                /*
                                 * Return array converted to object Using __FUNCTION__ (Magic constant)
                                 * for recursive call.
                                 */
                                print_r(array_map(__FUNCTION__, $d));
                        }
                        else
                        {
                                print_r($d);
                        }
                }
                elseif (is_bool($display))
                {
                     if ($display)
                     {
                         echo 'Boolean: true';
                     }
                     else
                     {
                         echo 'Boolean: false';
                     }
                }
                elseif (is_null($display))
                {
                    echo 'NULL';
                }
                else
                {
                        echo $display;
                }

                echo '</pre><font color="red">**END**</font>';
        }
}
