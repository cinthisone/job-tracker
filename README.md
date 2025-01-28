# Job Tracker - WordPress Plugin

### A simple WordPress plugin to store, search, and manage job application questions & answers.

![WordPress Plugin Screenshot](https://your-image-url.com/screenshot.jpg)

## 🚀 Features
- **Custom Post Type** (`job_questions`)
- **AJAX-powered Live Search**
- **Frontend Form with TinyMCE Editor**
- **Company & URL fields for tracking**
- **Secure REST API for Adding & Deleting**
- **Delete Button for quick management**

## 📌 Installation
1. **Download & Upload** to `/wp-content/plugins/qna-storage/`
2. **Activate** in **WordPress > Plugins**
3. **Use `[job_question_search]`** shortcode on any page

## 📖 Usage
- The **search bar** lets you find questions instantly.
- Click "**Add Question**" to enter:
  - ✅ **Title**
  - ✅ **Company Name** (Optional)
  - ✅ **URL** (Optional)
  - ✅ **Answer (WYSIWYG)**
- Each entry includes a **delete button** (for authorized users).

## 🔧 REST API
- **POST `/qna-storage/v1/add-question`** → Add new question
- **POST `/qna-storage/v1/delete-question`** → Delete question (requires permission)

## 🎉 Author
👨‍💻 **Chansamone Inthisone**  
🔗 GitHub: [cinthisone](https://github.com/cinthisone)
