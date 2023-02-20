# Crop Reference Overview
- This page used to manage cropped image details, crop newly uploaded product images, add new images
## Fetch data
- Process start from `grid` function of the `CroppedImageReferenceController` file.
- This grid will display data from `cropped_image_references` table with eloquent relationship and also join with `products` table.
- Defaults it gives `50` record grid.
- There are five filter condition applied and If any one is not empty then will apply filter while fetching records.
- Three count fetched from DB:
    - Total products is total number of records from `cropped_image_references` table.
    - Pending products count calculates based on status_id is 4($autoCrop) from same table. 
    - Pending categoroy products count calculate based on status_id is 33($attributeRejectCategory) and for this `stock` must be greater than or equals to 1.
- If `customer_range` requested and not null then total crop images for requested date.
# Listing
- After fetching data from `cropped_image_references` table then assign it to one datagrid. 
- Action column contain drop down to change status as Reject Product, Images Not Cropped Correctly, No Images Shown, Grid Not Shown, Blurry Image, First Image Not Available, Dimension Not Available, Wrong Grid Showing For Category, Incorrect Category, Only One Image Available, and Image incorrect.
- Last column issue contain issue detail and chat history.

# Filter
- There are five filters available in top of the page. Select or enter data in filter feilds and click on filter icon will filter data from DB and updated result will display in grid.
- Filters are productId, status, category, brands and supplier.

# Add Issue
- This functionality is used to submit an issue with creating new task and new branch on github for selected image from grid.
- First we need to select images from datagrid then click on `Add Issue` button which will open popup to store issue.
- Inside popup shows two inputs subject and assignee
- Click on `Add` to button from this popup then request come to `issueStore` method in the `DevelopmentController` file.
- In issueStore function first request validating creating then after create a new task to `developer_tasks` table.
- Checking if Issue is already exist or not If it exists then return to back.
- Finding `developer_modules` is exist or not If it not exists then it will create new developer module.
- Creating new task `developer_tasks` table.
- Checking github repositoy in `github_repositories` table If it' exists then it will create a new branch in github
- If `images` file requested then it uploads to `issue/task_id/image_per_folder/` folder and attach with this task.
- **sendMessage**
    - User will be notified on WhatsApp when new issue assigned to them.
    - Creating a POST request with two request paramters. First is requestData array which additionaly contain `issue_id`, `message`, and `status` while second parameter is context which value is `issue`.
    - Calling `sendMessage` function by passing both parameter as argument.
    - `sendMessage` function(WhatsAppController.php) is first validate request parameters. Next it conditionaly check for the second argument requested for `$context`.
    - In our case context is set to 'issue' so it will send message to whom task assign
    - If task assigned to `to_master` then he will receive message. Same for `to_team_lead` and `to_tester`.
    - Checking request_type from request and call `sendWithThirdApi`function.
    - This function will log message request in log file and send message to WhatsApp by specific domain. - Domain can select from is user admin or user's whatsapp number has provider or not
# Reject Image
- This functionality is used to update product to status rejected.
- First we need to select images from datagrid then after When click on `Reject Image` then shows reject cropping reason dropdown
- After selecting reason then request come to `rejectCrop` method in the `ProductCropperController` file.
- If product status_id is 18(cropRejected) OR 22(manualImageUpload) then it request is to return.
- If remark is `Image incorrect` then change product status_id to 22(manualImageUpload) otherwise it will set to 18(cropRejected).
- Updatting product crop related columns with setting `is_crop_rejected` to 1.
- Checking `crop_approved_by` > 0 then create new entry to `listing_histories` table by setting action to `CROP_APPROVAL_DENIED`.
- Creating new entry to `listing_histories` table by setting action to `CROP_REJECTED`.
- Creating new entry to `user_product_feedbacks` table by setting action to `CROP_APPROVAL_REJECTED`.
# Instances
- Instance is setup to cropping of uploaded image and that can manage from `Instance` button from top right corner of this page.
- Once we hit this button will open popup from where user can manage instances. 
- Multiple instances are added for having more capactiy on cropping.
- New instance can directly add from top section of popup by entring instace id and comment
- Same popup contain list of all saved instance. This list contain information of Instace Id, Comment, Created date and Action column
- Action column contain four icons named start instance, stop instance, delete instance and log instance. 
- When user start any instance it will find instance from `cropping_instance` table and call API on another server to start cropping from Python script
- API URL is "config('constants.py_crop_script').'/start'" where py_crop_script defined either in config file of env file.
- Stop instance will stop process of cropping on another server by requesting "config('constants.py_crop_script').'/stop'" API
- Delete instance will delete requested instance from `cropping_instance` table.
- Log instance will get logs from http://173.212.203.50:100/get-logs API by providing instance id and date in request(This date can select from top right corner of Manage Crop Instances popup).  