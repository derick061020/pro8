<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Portada enriquecida para las entradas de blog: además de imagen, la portada
 * puede ser un video (YouTube, Vimeo o embed). El contenido pasa a ser HTML
 * enriquecido (CKEditor), por eso ya se guarda como longText.
 */
class AddCoverFieldsToHotelBlogPostsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('hotel_blog_posts')) {
            return;
        }

        Schema::table('hotel_blog_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_blog_posts', 'cover_type')) {
                // 'image' | 'video'
                $table->string('cover_type', 20)->default('image')->after('image');
            }
            if (!Schema::hasColumn('hotel_blog_posts', 'video_url')) {
                $table->string('video_url', 500)->nullable()->after('cover_type');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('hotel_blog_posts')) {
            return;
        }

        Schema::table('hotel_blog_posts', function (Blueprint $table) {
            if (Schema::hasColumn('hotel_blog_posts', 'video_url')) {
                $table->dropColumn('video_url');
            }
            if (Schema::hasColumn('hotel_blog_posts', 'cover_type')) {
                $table->dropColumn('cover_type');
            }
        });
    }
}
