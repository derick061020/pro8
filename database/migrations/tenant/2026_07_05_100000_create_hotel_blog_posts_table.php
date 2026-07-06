<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Entradas de blog que se muestran en la web pública de reservas (landing).
 * Cada tenant administra sus propias notas desde Hotel -> Blog.
 */
class CreateHotelBlogPostsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('hotel_blog_posts')) {
            return;
        }

        Schema::create('hotel_blog_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 191);
            $table->string('slug', 191)->nullable();
            $table->string('author', 120)->nullable();
            $table->string('image', 191)->nullable();
            $table->string('excerpt', 500)->nullable();
            $table->longText('content')->nullable();
            $table->boolean('published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('establishment_id')->nullable();
            $table->timestamps();

            $table->index('slug');
            $table->index('establishment_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('hotel_blog_posts');
    }
}
