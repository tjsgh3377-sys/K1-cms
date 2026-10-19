# K1 CMS 部署指南 (中文)

## 📝 功能更新说明

本次更新为文章（游戏/新闻）模块添加了：
- ✨ **多图像上传** - 支持一次上传多张图片
- 📎 **文件附件** - 支持 PDF、Word、Excel 等文件下载

---

## 🚀 部署步骤

### **步骤 1️⃣ 更新代码**

从 GitHub 拉取最新代码：

```bash
git pull origin main
```

---

### **步骤 2️⃣ 数据库迁移**

#### **方式 A：使用 PHP（推荐）**

```bash
php spark migrate
```

#### **方式 B：手动执行 SQL**

在 PhpMyAdmin 或 MySQL 客户端执行以下语句：

```sql
ALTER TABLE articles ADD COLUMN images JSON NULL COMMENT '文章图片（JSON 数组）';
ALTER TABLE articles ADD COLUMN attachments JSON NULL COMMENT '文件附件（JSON 数组）';
```

---

### **步骤 3️⃣ 创建上传文件夹**

创建用于存储图片和附件的文件夹：

```bash
mkdir -p public/uploads/articles/images
mkdir -p public/uploads/articles/attachments
chmod 755 public/uploads/articles/images
chmod 755 public/uploads/articles/attachments
```

**文件夹结构：**
```
public/
  └── uploads/
       └── articles/
            ├── images/        (存储上传的图片)
            └── attachments/   (存储上传的文件)
```

---

## 📊 技术细节

### 修改的文件

| 文件 | 修改内容 |
|------|--------|
| `app/Database/Migrations/2026-10-16-150000_AddImageToArticles.php` | 新增迁移文件 |
| `app/Models/ArticleModel.php` | 添加 images、attachments 字段 |
| `app/Controllers/Admin/Article.php` | 实现多文件上传处理 |
| `app/Views/admin/article_form.php` | 添加图片和文件输入字段 |
| `app/Views/admin/article_list.php` | 显示图片/文件数量标志 |
| `app/Views/site/news_view.php` | 前端显示图片库和下载链接 |

### 支持的文件格式

**图片：** JPG、PNG、GIF、WebP（最大 5MB）

**附件：** PDF、DOC、DOCX、XLS、XLSX、PPT、PPTX、HWP、ZIP、TXT（最大 10MB）

### 数据存储

文件信息以 JSON 格式存储在数据库中：

```json
// images 字段示例
[
  {
    "path": "uploads/articles/images/abc123.jpg",
    "name": "screenshot.jpg"
  },
  {
    "path": "uploads/articles/images/def456.png",
    "name": "diagram.png"
  }
]

// attachments 字段示例
[
  {
    "path": "uploads/articles/attachments/file123.pdf",
    "name": "manual.pdf",
    "size": 2048576
  }
]
```

---

## ✅ 验证部署

部署完成后，验证以下内容：

### 1. 数据库检查

```sql
-- 检查 articles 表结构
DESC articles;

-- 应该看到新增的两列：
-- - images (JSON)
-- - attachments (JSON)
```

### 2. 管理后台检查

访问管理后台的文章编辑页面：
- `https://k1tnc.co.kr/admin/article/create` (新建)
- `https://k1tnc.co.kr/admin/article/edit/[ID]` (编辑)

应该看到：
- ✅ "🖼️ 图片"输入字段（支持多文件）
- ✅ "📎 文件附件"输入字段（支持多文件）
- ✅ 选择文件后显示预览

### 3. 前端检查

访问游戏/新闻详情页面：
- `https://k1tnc.co.kr/news/view/[ID]`

应该看到：
- ✅ 文章标题下方显示图片库
- ✅ 内容下方显示可下载的文件列表

---

## 🐛 常见问题

### Q: 文件无法上传？

**A:** 检查文件夹权限

```bash
# Linux/Mac
chmod 755 public/uploads/articles/images
chmod 755 public/uploads/articles/attachments

# Windows (通常自动处理)
```

### Q: 数据库迁移失败？

**A:** 检查 PHP 和 MySQL 连接

```bash
# 查看 .env 文件的数据库配置
cat .env | grep database
```

### Q: 旧数据怎么办？

**A:** 无需操作。新增的 `images` 和 `attachments` 列为 NULL，不影响现有数据。

---

## 📞 技术支持

如有问题，请参考：

- CodeIgniter 4 文档：https://codeigniter.com/
- GitHub 仓库：https://github.com/tjsgh3377-sys/K1-cms

---

## 📋 部署检查清单

- [ ] 执行 `git pull origin main`
- [ ] 执行数据库迁移或 SQL 语句
- [ ] 创建上传文件夹（images 和 attachments）
- [ ] 设置文件夹权限（755）
- [ ] 验证管理后台显示新字段
- [ ] 验证前端显示图片和文件链接
- [ ] 测试上传功能

---

**部署完成！** ✨

K1 CMS 现已支持文章多图像和文件附件功能。

祝部署顺利！🎉
