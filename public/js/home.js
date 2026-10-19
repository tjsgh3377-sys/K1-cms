// ========== 포트폴리오 필터링 ==========
document.addEventListener('DOMContentLoaded', function() {
  const filterButtons = document.querySelectorAll('.filter-btn');
  const portfolioItems = document.querySelectorAll('.portfolio-item');

  filterButtons.forEach(button => {
    button.addEventListener('click', function() {
      const filter = this.dataset.filter;

      filterButtons.forEach(btn => btn.classList.remove('is-active'));
      this.classList.add('is-active');

      portfolioItems.forEach(item => {
        if (filter === 'all') {
          item.classList.remove('hidden');
        } else if (item.dataset.category === filter) {
          item.classList.remove('hidden');
        } else {
          item.classList.add('hidden');
        }
      });
    });
  });
});

// ========== 네비게이션 토글 ==========
const navToggle = document.getElementById('navToggle');
const navDrawer = document.getElementById('navDrawer');

if (navToggle && navDrawer) {
  navToggle.addEventListener('click', function() {
    navDrawer.classList.toggle('is-open');
    navDrawer.setAttribute('aria-hidden', navDrawer.classList.contains('is-open') ? 'false' : 'true');
  });

  // 메뉴 클릭 시 드로어 닫기
  navDrawer.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', function() {
      navDrawer.classList.remove('is-open');
      navDrawer.setAttribute('aria-hidden', 'true');
    });
  });
}

// ========== 고정 헤더 효과 ==========
const header = document.getElementById('header');
let lastScrollY = 0;

window.addEventListener('scroll', function() {
  lastScrollY = window.scrollY;
  if (lastScrollY > 0) {
    header.classList.add('is-sticky');
  } else {
    header.classList.remove('is-sticky');
  }
});

// ========== 부드러운 스크롤 ==========
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    const href = this.getAttribute('href');
    if (href === '#') return;

    e.preventDefault();
    const target = document.querySelector(href);
    if (target) {
      target.scrollIntoView({ behavior: 'smooth' });
    }
  });
});

// ========== 문의 양식 제출 ==========
const inquiryForm = document.getElementById('inquiryForm');
if (inquiryForm) {
  inquiryForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    // 유효성 검사
    if (!data.service) {
      alert('서비스를 선택해주세요.');
      return;
    }
    if (!data.name || !data.email || !data.phone) {
      alert('필수 정보를 입력해주세요.');
      return;
    }
    if (!data.message) {
      alert('프로젝트 내용을 입력해주세요.');
      return;
    }

    try {
      const response = await fetch(this.action, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
      });

      const result = await response.json();

      if (result.status) {
        alert('상담 신청이 완료되었습니다. 빠른 시간 내에 연락드리겠습니다!');
        this.reset();
      } else {
        alert('상담 신청 중 오류가 발생했습니다. 다시 시도해주세요.');
      }
    } catch (error) {
      console.error('Error:', error);
      alert('서버 오류가 발생했습니다. 나중에 다시 시도해주세요.');
    }
  });
}

// ========== 숫자 카운트 애니메이션 (옵션) ==========
function animateCounter(element, target, duration = 2000) {
  let current = 0;
  const increment = target / (duration / 16);

  const timer = setInterval(() => {
    current += increment;
    if (current >= target) {
      element.textContent = target;
      clearInterval(timer);
    } else {
      element.textContent = Math.floor(current);
    }
  }, 16);
}

// ========== Intersection Observer - 스크롤 시 요소 나타나기 ==========
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
    }
  });
}, observerOptions);

// 관찰 대상 설정
document.querySelectorAll('.service-card, .portfolio-item, .news-card').forEach(element => {
  element.style.opacity = '0';
  element.style.transform = 'translateY(20px)';
  element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
  observer.observe(element);
});

// ========== 액티브 네비게이션 강조 ==========
function updateActiveNavigation() {
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav__link');

  window.addEventListener('scroll', () => {
    let current = '';

    sections.forEach(section => {
      const sectionTop = section.offsetTop;
      const sectionHeight = section.clientHeight;

      if (scrollY >= sectionTop - 200) {
        current = section.getAttribute('id');
      }
    });

    navLinks.forEach(link => {
      link.classList.remove('is-active');
      if (link.getAttribute('href').slice(1) === current) {
        link.classList.add('is-active');
      }
    });
  });
}

updateActiveNavigation();

// ========== 로컬 스토리지에서 폼 데이터 복원 (옵션) ==========
if (inquiryForm) {
  // 저장
  inquiryForm.addEventListener('change', () => {
    const formData = new FormData(inquiryForm);
    localStorage.setItem('inquiryForm', JSON.stringify(Object.fromEntries(formData)));
  });

  // 복원
  const savedData = localStorage.getItem('inquiryForm');
  if (savedData) {
    const data = JSON.parse(savedData);
    Object.keys(data).forEach(key => {
      const input = inquiryForm.elements[key];
      if (input) {
        input.value = data[key];
      }
    });
  }
}

console.log('홈페이지 스크립트 로드 완료');
