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

# Filter
- There are five filter available in top of the page when click on filter button icon then it will applying.
- filters are productId, status, category, brands and supplier.

# Add Issue
- This functionality is used to submit an issue with creating new task and new branch on github.
- First we need to select images from datagrid then after When click on `Add Issue` button then it's shows one popup of Store a Issue.
- Inside popup shows two inputs subject and assignee
- Click on `Add` to button in the `Store a Issue` popup then request come to `issueStore` method in the `DevelopmentController` file.
- In issueStore function first request validating creating then after create a new task to `developer_tasks` table.
- Checking if Issue is already exist or not If it exists then return to back.
- Finding `developer_modules` is exist or not If it not exists then it will create new developer module.
- Creating new task `developer_tasks` table.
- Checking github repositoy in `github_repositories` table If it' exists then it will create a new branch in github
- If `images` requested then uploading image and then attach it to task.
- **sendMessage**
    - Creating a POST request and paramters are `issue_id`, `message`, and `status`.
    - Calling `sendMessage` function by passing the request.
    - In side this function checking `$context` is `issue` condition
    - Checking sender relevetion conditions like `to_master`,`to_team_lead` and `to_tester`.
    - Checking request type conditions and after next process to `sendWithThirdApi`function
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