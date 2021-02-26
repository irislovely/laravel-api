## About This Project

Using Laravel to create an API that simulating the uploading process of files, having some constrains in the file being uploaded.


- GET **/api/providers**: List all providers
- GET **/api/files**: List all uploaded files
- POST **/api/files**: Upload a file


## File parameters:

- **name**: The name for that file
- **provider_id**: The id of the provider
- **file**: The file object (image or video files only)


## Models:

- **providers** to store all the providers
    
    |       |  |
    | ----------- | ----------- |
    | **id**      | The id of provider       |
    | **name**   | The name of provider        |  

- files to store all the uploaded files

    |       |  |
    | ----------- | ----------- |
    | **id**      | The id of file       |
    | **name**   | The name of file        |
    | **filepath**   | The path to the file on hosting        |
    | **thumb**   | The path to the file on hosting (if file is video)        |
    | **type**   | The extension of the file        |
    | **provider_id**   | The id of provider - foreign_key        |  
    
- **providers** and **files** have One-To-Many relationship


## PHPUnit tests

Have PHPUnit test class **FilesInteractionTest** to do testing on 4 features:

- **test_can_get_providers_list**: Test to see if can retrieve providers list
- **test_can_get_files_list**: Test to see if can retrieve files list
- **test_required_fields_when_create_file**: Test to see if fields **required** is working
- **test_can_create_file_complete**: Test to see if a file can go through all processes and successfully created


## Factory and seeders

Prepared seeder file to run **php artisan db:seeder** after migration


## Files filter

Files list can be filter by 2 attributes: **type** and **date**

http://localhost:8080/api/files?type=jpg

http://localhost:8080/api/files?date=2021-02-26

