<?php

	class OhioExtraFilter {

		/**
		* String filtering
		*/
		static public function string( $string, $filter_type = 'string', $default = false ) {
			$filter_result = $default;
			$string = wp_check_invalid_utf8( trim( $string ) );
			switch ( $filter_type ) {
				case 'attr':
					$string = esc_attr( $string );
					break;
				case 'url':
					$string = esc_url( urldecode( $string ) );
					break;
			}
			if ( !empty( $string ) || $string === '0' ) {
				$filter_result = $string;
			}
			return $filter_result;
		}

		/**
		* Boolean filtering
		*/
		static public function boolean( $value, $default = NULL ) {
			if ( $default !== NULL && $value === NULL ) {
				$value = $default;
			}
			return (bool) $value;
		}

		/**
		* Heading tag filtering
		*/
		static public function headingTag( $tag, $default = 'h3', $allow_div = false ) {
			$allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
			if ( $allow_div ) {
				$allowed_tags[] = 'div';
			}
			if ( in_array( $tag, $allowed_tags ) ) {
				return $tag;
			}
			return $default;
		}

		/**
		* Dynamic Text "Letter Reveal" timing (ms) per speed preset
		*/
		static public function dynamicTextRevealTiming( $speed ) {
			switch ( $speed ) {
				case 'slow':
					return array( 'duration' => 700, 'stagger' => 45, 'hold' => 2200 );
				case 'fast':
					return array( 'duration' => 400, 'stagger' => 20, 'hold' => 700 );
				case 'normal':
				default:
					return array( 'duration' => 550, 'stagger' => 30, 'hold' => 1200 );
			}
		}
	}
