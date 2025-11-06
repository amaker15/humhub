<?php

use humhub\components\Migration;
use yii\db\Query;

class m251106_134911_connectabroad_travel_fields extends Migration
{
    public function up()
    {
        // Create "Travel" profile field category
        $this->insert('profile_field_category', [
            'title' => 'Travel',
            'description' => 'Travel and study abroad information',
            'sort_order' => 100,
            'translation_category' => 'ConnectAbroadModule.profile',
            'module_id' => 'connectabroad',
            'visible' => 1,
            'is_system' => 0,
        ]);

        // Get the category ID
        $categoryId = $this->db->getLastInsertID();

        // Add Current Destination field (dropdown)
        $this->insert('profile_field', [
            'profile_field_category_id' => $categoryId,
            'field_type_class' => 'ProfileFieldTypeSelect',
            'field_type_config' => '{"options":"Barcelona=>Barcelona, Spain\r\nTokyo=>Tokyo, Japan\r\nBerlin=>Berlin, Germany\r\nParis=>Paris, France\r\nLondon=>London, UK\r\nAmsterdam=>Amsterdam, Netherlands\r\nSydney=>Sydney, Australia\r\nNew York=>New York, USA\r\nOther=>Other"}',
            'internal_name' => 'current_destination',
            'title' => 'Current Destination',
            'description' => 'Where are you currently studying abroad?',
            'sort_order' => 100,
            'required' => 0,
            'show_at_registration' => 1,
            'editable' => 1,
            'visible' => 1,
            'searchable' => 1,
            'directory_filter' => 1,
            'translation_category' => 'ConnectAbroadModule.profile',
        ]);

        // Add Study Program/University field (text)
        $this->insert('profile_field', [
            'profile_field_category_id' => $categoryId,
            'field_type_class' => 'ProfileFieldTypeText',
            'field_type_config' => '{"minLength":2,"maxLength":255,"validator":"","default":"","placeholder":"e.g., Erasmus at University of Barcelona"}',
            'internal_name' => 'study_program',
            'title' => 'Study Program/University',
            'description' => 'Your study abroad program and university',
            'sort_order' => 200,
            'required' => 0,
            'show_at_registration' => 1,
            'editable' => 1,
            'visible' => 1,
            'searchable' => 1,
            'directory_filter' => 0,
            'translation_category' => 'ConnectAbroadModule.profile',
        ]);

        // Add Interests field (multi-select tags)
        $this->insert('profile_field', [
            'profile_field_category_id' => $categoryId,
            'field_type_class' => 'ProfileFieldTypeTags',
            'field_type_config' => '{"options":"hiking=>Hiking\r\nlanguage-exchange=>Language Exchange\r\ncultural-events=>Cultural Events\r\nsports=>Sports\r\nmusic=>Music\r\nfood=>Food & Cooking\r\nphotography=>Photography\r\nvolunteering=>Volunteering\r\nnightlife=>Nightlife\r\nbeach=>Beach Activities\r\nmountains=>Mountain Activities\r\ncity-exploration=>City Exploration\r\nart=>Art & Museums\r\ntechnology=>Technology\r\nbusiness=>Business & Networking"}',
            'internal_name' => 'interests',
            'title' => 'Interests',
            'description' => 'What activities and interests do you enjoy?',
            'sort_order' => 300,
            'required' => 0,
            'show_at_registration' => 1,
            'editable' => 1,
            'visible' => 1,
            'searchable' => 1,
            'directory_filter' => 1,
            'translation_category' => 'ConnectAbroadModule.profile',
        ]);

        // Add Travel Status field (select)
        $this->insert('profile_field', [
            'profile_field_category_id' => $categoryId,
            'field_type_class' => 'ProfileFieldTypeSelect',
            'field_type_config' => '{"options":"seeking-roommates=>Seeking Roommates\r\nseeking-study-buddies=>Seeking Study Buddies\r\nopen-to-meet=>Open to Meet New People\r\njust-exploring=>Just Exploring\r\nlong-term-resident=>Long-term Resident\r\nreturning-home=>Returning Home Soon"}',
            'internal_name' => 'travel_status',
            'title' => 'Travel Status',
            'description' => 'What are you looking for in your study abroad experience?',
            'sort_order' => 400,
            'required' => 0,
            'show_at_registration' => 1,
            'editable' => 1,
            'visible' => 1,
            'searchable' => 1,
            'directory_filter' => 1,
            'translation_category' => 'ConnectAbroadModule.profile',
        ]);

        // Add database columns
        $this->addColumn('profile', 'current_destination', 'varchar(255) DEFAULT NULL');
        $this->addColumn('profile', 'study_program', 'varchar(255) DEFAULT NULL');
        $this->addColumn('profile', 'interests', 'text DEFAULT NULL');
        $this->addColumn('profile', 'travel_status', 'varchar(255) DEFAULT NULL');
    }

    public function down()
    {
        // Remove the Travel category and its fields
        $category = (new Query())
            ->select('id')
            ->from('profile_field_category')
            ->where(['title' => 'Travel'])
            ->one();

        if ($category) {
            // Delete profile fields
            $this->delete('profile_field', ['profile_field_category_id' => $category['id']]);

            // Delete category
            $this->delete('profile_field_category', ['id' => $category['id']]);
        }

        // Drop columns
        $this->dropColumn('profile', 'current_destination');
        $this->dropColumn('profile', 'study_program');
        $this->dropColumn('profile', 'interests');
        $this->dropColumn('profile', 'travel_status');

        return true;
    }
}
