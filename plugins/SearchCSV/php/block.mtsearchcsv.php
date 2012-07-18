<?php
function smarty_block_mtsearchcsv ( $args, $content, &$ctx, &$repeat ) {
    $localvars = array( '__mtsearchcsv_counter', '__mtsearchcsv_match_count',  '__mtsearchcsv_contents' );
    if (! isset( $content ) ) {
        $ctx->localize( $localvars );
        $ctx->stash( '__mtsearchcsv_counter', 0 );
        $file  = $args[ 'file' ];
        $cell  = $args[ 'cell' ];
        $regex = $args[ 'regex' ];
        $and_or = $args[ 'and_or' ];
        if (! $and_or ) {
            $and_or = 'AND';
        } else {
            $and_or = strtoupper( $and_or );
        }
        if ( $cell ) {
            $cell = str_getcsv( $cell, ':' );
        }
        if ( $regex ) {
            $regex = str_getcsv( $regex, ':' );
        }
        $fp = fopen( $file, 'r' );
        $res = array();
        while ( $csv = fgetcsv( $fp ) ) {
            $match = TRUE;
            if ( $cell && $regex ) {
                $match = FALSE;
                $i = 0;
                foreach ( $cell as $num ) {
                    $data = $csv[ $num ];
                    if ( preg_match( $regex[ $i ], $data ) ) {
                        $match = TRUE;
                        if ( $and_or == 'OR' ) {
                            break;
                        }
                    } else {
                        if ( $and_or == 'AND' ) {
                            $match = FALSE;
                            break;
                        }
                    }
                    $i++;
                }
            }
            if ( $match ) {
                array_push( $res, $csv );
            }
            //var_dump($csv);
        }
        fclose( $fp );
        $ctx->stash( '__mtsearchcsv_contents', $res );
        $ctx->stash( '__mtsearchcsv_match_count', count( $res ) );
        //var_dump( $res );
    } else {
        $counter = $ctx->stash( '__mtsearchcsv_counter' );
        $contents = $ctx->stash( '__mtsearchcsv_contents' );
        $max = $ctx->stash( '__mtsearchcsv_match_count' );
        $count = $counter + 1;
        if ( $counter < $max ) {
            $csv = $contents[ $counter ];
            $counter++;
            $ctx->stash( '__mtsearchcsv_counter', $counter );
            $ctx->__stash[ 'vars' ][ '__counter__' ] = $count;
            $ctx->__stash[ 'vars' ][ '__odd__' ]  = ( $count % 2 ) == 1;
            $ctx->__stash[ 'vars' ][ '__even__' ] = ( $count % 2 ) == 0;
            $ctx->__stash[ 'vars' ][ '__first__' ] = $count == 1;
            $ctx->__stash[ 'vars' ][ '__last__' ] = ( $count == $max );
            $i = 0;
            foreach ( $csv as $item ) {
                $ctx->__stash[ 'vars' ][ '__item_' . $i ] = $item;
                $i++;
            }
            // var_dump( $csv );
            $repeat = TRUE;
        } else {
            $ctx->restore( $localvars );
            $repeat = FALSE;
        }
    }
    return $content;
}
?>