# **Exporting and Importing Web Stories**

## **Step 1: Prepare Both Sites**  
1. Disable the `.htaccess` file on the **export site**.  
2. Install the **Web Stories Content Transfer** plugin and the **Web Stories Export Import** plugin on **both sites**.  

## **Step 2: Export Web Stories**  
1. In the **export site**, go to **Tools → Export**.  
2. Select the **Stories** radio button.  
3. Click **Download Export File** to save the XML file.  

## **Step 3: Export Media**  
1. In the **export site**, go to **Tools → Export**.  
2. Select the **Media** radio button.  
3. Click **Download Export File** to save the XML file.  

## **Step 4: Export Web Stories Metadata**  
1. In the **export site**, go to **Tools → Export Web Stories Content**.  
2. Click **Download CSV** to save the metadata file.  

## **Step 5: Import Web Stories and Media**  
1. In the **import site**, go to **Tools → Import → WordPress**.  
2. Click **Install Now / Run Importer**.  
3. Upload and import the **Stories XML file** (from Step 2).  
4. Repeat the process for the **Media XML file** (from Step 3).  

## **Step 6: Configure Import Settings**  
1. During the import process, assign posts to an existing user.  
2. Tick **Download and import file attachments**.  
3. Click **Submit** to complete the import.  

## **Step 7: Import Web Stories Metadata**  
1. In the **import site**, go to **Tools → Import Web Stories Content**.  
2. Upload the **CSV file** downloaded in Step 4.  
3. Click **Import** to complete the process.  

## **Step 8: Finalize the Export Site**  
1. Add the `.htaccess` file back to the **export site**.  

### **Notes:**  
- After importing the Stories XML file, Media XML file, and CSV file, the stories should be editable.  
- Ensure all files are correctly uploaded to avoid missing content.
