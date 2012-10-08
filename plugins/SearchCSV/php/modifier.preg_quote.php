<?php
function smarty_modifier_preg_quote ( $text, $delimiter ) {
    if ( $delimiter == 1 ) {
        $delimiter = '/';
    }
    return preg_quote( $text, $delimiter );
}
?>