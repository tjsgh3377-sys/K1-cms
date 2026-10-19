# 케이원 홈페이지 - 디자인 인쇄 전문 회사 버전

## 📋 개요

명함, 리플렛, 로고, 스티커 제작 전문 회사 **케이원**의 홈페이지입니다.
**쓰담마케팅(ssdam.co.kr)** 스타일의 미니멀하고 효율적인 구조로 리뉴얼되었습니다.

---

## 🎯 주요 기능

### 1️⃣ 홈페이지 구조
- **고정 헤더**: About, Works, Contact 3개 메뉴
- **히어로 섹션**: 임팩트 있는 메인 이미지/슬로건
- **회사 소개**: 미션 및 비전
- **4대 사업 기둥**: 명함, 리플렛, 로고, 스티커 (색상 구분)
- **포트폴리오**: 카테고리별 필터링 가능
- **클라이언트**: 거래처 로고 표시
- **최근 소식**: 블로그/뉴스 3개 항목
- **문의 양식**: 3단계 (서비스 선택 → 담당자정보 → 상세내용)
- **푸터**: 회사 정보, SNS, 빠른링크
- **플로팅 액션 버튼**: 전화, 카카오톡, 문의하기

### 2️⃣ 포트폴리오 필터링
```javascript
// 카테고리: card, leaflet, logo, sticker
ALL / 명함 / 리플렛 / 로고 / 스티커
```

### 3️⃣ 반응형 디자인
- 데스크톱, 태블릿, 모바일 모두 최적화
- 모바일 햄버거 메뉴

### 4️⃣ API 기반 문의 처리
```
POST /api/inquiry/submit
- service: card|leaflet|logo|sticker|other
- name: string (필수)
- email: string (필수)
- phone: string (필수)
- company: string (선택)
- message: string (필수)
```

---

## 📁 파일 구조

```
app/
├── Views/site/pages/
│   └── index.php                    ← 홈페이지 (쓰담마케팅 스타일)
├── Controllers/Api/
│   └── Inquiry.php                  ← 문의 API 컨트롤러
├── Models/
│   ├── InquiryModel.php             ← 업데이트 (service 필드 추가)
│   └── GalleryModel.php             ← 업데이트 (category 필드 추가)
├── Database/Migrations/
│   ├── 2026-10-18-150000_UpdateInquiries.php        ← 신규
│   └── 2026-10-18-160000_AddCategoryToGalleries.php ← 신규
└── Config/
    └── Routes.php                   ← 업데이트 (inquiry API 라우트 추가)

public/
├── css/
│   └── home.css                     ← 홈페이지 스타일 (신규)
├── js/
│   └── home.js                      ← 포트폴리오 필터링, 폼 처리 (신규)
```

---

## 🚀 설치 및 실행

### 1. 마이그레이션 실행
```bash
php spark migrate
```

변경 사항:
- `inquiries` 테이블: `purpose` → `service` 필드로 변경
- `inquiries` 테이블: `status`, `ip_address` 필드 추가
- `galleries` 테이블: `category`, `description` 필드 추가

### 2. 로컬 서버 실행
```bash
php spark serve
```

접속: `http://localhost:8080`

---

## 📝 문의 양식 흐름

### 1단계: 서비스 선택
```html
<input type="radio" name="service" value="card"> 명함 제작
<input type="radio" name="service" value="leaflet"> 리플렛 제작
<input type="radio" name="service" value="logo"> 로고 제작
<input type="radio" name="service" value="sticker"> 스티커 제작
<input type="radio" name="service" value="other"> 기타 상담
```

### 2단계: 담당자 정보
- name (성명) - 필수
- email (이메일) - 필수
- phone (연락처) - 필수
- company (회사명) - 선택

### 3단계: 상세 내용
- message (프로젝트 내용) - 필수

---

## 🎨 포트폴리오 관리

### 관리자 페이지
```
/admin/gallery
```

### 추가할 정보
- **title**: 포트폴리오 제목 (예: "브런치 카페 명함")
- **category**: card|leaflet|logo|sticker
- **description**: 포트폴리오 설명 (예: "카페 브랜드의 따뜻한 감성을 담은 명함")
- **image_path**: 포트폴리오 이미지 (필수)
- **thumb_path**: 썸네일 이미지

---

## 🎬 포트폴리오 필터링 (JS)

```javascript
// home.js 에서 구현됨
// 클릭한 버튼의 data-filter 값에 따라 portfolio-item을 필터링

.filter-btn[data-filter="card"]      // 명함만 표시
.filter-btn[data-filter="leaflet"]   // 리플렛만 표시
.filter-btn[data-filter="logo"]      // 로고만 표시
.filter-btn[data-filter="sticker"]   // 스티커만 표시
.filter-btn[data-filter="all"]       // 모두 표시
```

---

## 🔧 API 응답 포맷

### 성공 (201 Created)
```json
{
  "status": true,
  "message": "상담 신청이 완료되었습니다.",
  "inquiry_id": 1
}
```

### 실패 (400 Bad Request)
```json
{
  "status": false,
  "message": "입력 정보가 올바르지 않습니다.",
  "errors": {
    "service": "서비스를 선택해 주세요."
  }
}
```

---

## 📊 데이터베이스 스키마

### inquiries (문의)
```sql
CREATE TABLE inquiries (
  id INT PRIMARY KEY AUTO_INCREMENT,
  service VARCHAR(50),                    -- card|leaflet|logo|sticker|other
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  company VARCHAR(100),
  message TEXT NOT NULL,
  status VARCHAR(20) DEFAULT 'new',       -- new|contacted|completed|rejected
  ip_address VARCHAR(50),
  is_read TINYINT(1) DEFAULT 0,
  created_at DATETIME,
  updated_at DATETIME,
  deleted_at DATETIME
);
```

### galleries (포트폴리오)
```sql
ALTER TABLE galleries ADD COLUMN (
  category VARCHAR(50),                   -- card|leaflet|logo|sticker
  description TEXT
);
```

---

## 🎨 CSS 색상 및 스타일

### 주요 색상
- **Primary**: #667eea (보라색)
- **Secondary**: #764ba2 (진보라)
- **Accent colors**:
  - Card: #667eea
  - Leaflet: #f5576c (빨강)
  - Logo: #4facfe (파랑)
  - Sticker: #fa709a (핑크)

### 반응형 breakpoint
- **Desktop**: 1200px 이상
- **Tablet**: 768px ~ 1199px
- **Mobile**: 480px ~ 767px
- **Small Mobile**: 480px 미만

---

## 🐛 트러블슈팅

### 문의 양식이 제출되지 않음
1. 개발자 도구 > Network 탭에서 `/api/inquiry/submit` 요청 확인
2. 마이그레이션이 실행되었는지 확인: `php spark migrate`
3. InquiryModel의 allowedFields에 필드가 포함되었는지 확인

### 포트폴리오 필터링이 작동하지 않음
1. `home.js` 로드 확인
2. 브라우저 콘솔에서 오류 메시지 확인
3. portfolio-item의 `data-category` 속성이 올바른지 확인

### 마이그레이션 오류
```bash
# 마이그레이션 상태 확인
php spark migrate:status

# 특정 마이그레이션만 실행
php spark migrate --namespace App
```

---

## 📞 API 엔드포인트

| Method | URL | 설명 |
|--------|-----|------|
| POST | `/api/inquiry/submit` | 문의 양식 제출 |
| GET | `/` | 홈페이지 |
| GET | `/admin/gallery` | 포트폴리오 관리 (관리자) |
| GET | `/admin/inquiry` | 문의 목록 (관리자) |

---

## 📌 주의사항

1. **로드 순서**: CSS → JS 순서를 유지해야 함
2. **CSRF 토큰**: API 요청에 `X-Requested-With: XMLHttpRequest` 헤더 필수
3. **이미지 최적화**: 포트폴리오 이미지는 웹 최적화 필요 (권장: 800x600 이상)
4. **브라우저 지원**: IE 미지원 (모던 브라우저만)

---

## 📚 참고 문서

- CodeIgniter 4: https://codeigniter.com
- 쓰담마케팅: https://ssdam.co.kr
- 킹콩디자인: https://kk-d.com

---

**마지막 업데이트**: 2026-10-18  
**개발자**: 케이원 개발팀
