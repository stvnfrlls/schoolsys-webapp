const ROLE_PILLS = [
    { label: 'Admin Dashboard', color: '#c4b5fd', cls: 'float-a' },
    { label: 'Faculty Portal', color: '#38bdf8', cls: 'float-b' },
    { label: 'Student Self-Service', color: '#34d399', cls: 'float-a' },
    { label: 'Parent Access', color: '#fbbf24', cls: 'float-b' },
];

const STATS = [
    { target: 2400, suffix: '+', label: 'Students Managed', acc: 'acc-sky' },
    { target: 180, suffix: '+', label: 'Faculty Members', acc: 'acc-violet' },
    { target: 98, suffix: '%', label: 'Uptime Reliability', acc: 'acc-emerald' },
    { target: 50, suffix: '+', label: 'Institutions Trust Us', acc: 'acc-amber' },
];

const ROLE_CARDS = [
    {
        chipClass: 'chip-admin',
        chipLabel: 'Admin',
        bg: 'linear-gradient(135deg, #faf5ff 0%, #fff 60%)',
        hoverBorder: 'hover:border-violet-200',
        title: 'Full Institutional Control',
        desc: 'Oversee enrollment, manage users, configure roles, run reports, and access every corner of the platform.',
        items: ['User & role management', 'Activity audit logs', 'Institution-wide analytics'],
        checkColor: '#7c3aed',
        extraClass: '',
        delay: '0s',
    },
    {
        chipClass: 'chip-faculty',
        chipLabel: 'Faculty',
        bg: 'linear-gradient(135deg, #fffbeb 0%, #fff 60%)',
        hoverBorder: 'hover:border-amber-200',
        title: 'Teach Without the Paperwork',
        desc: 'Record grades and attendance, post assignments, track submissions, and communicate with students.',
        items: ['Gradebook management', 'Attendance tracking', 'Assignment creation & review'],
        checkColor: '#d97706',
        extraClass: '',
        delay: '0.1s',
    },
    {
        chipClass: 'chip-student',
        chipLabel: 'Student',
        bg: 'linear-gradient(135deg, #f0fdf4 0%, #fff 60%)',
        hoverBorder: 'hover:border-emerald-200',
        title: 'Everything You Need to Succeed',
        desc: 'Check your schedule, view grades, submit assignments, and track academic progress from any device.',
        items: ['Real-time grade visibility', 'Schedule & class information', 'Online assignment submission'],
        checkColor: '#059669',
        extraClass: 'sm:col-span-2 md:col-span-1',
        delay: '0.2s',
    },
];

const CHECK_ICON = `<svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/>
</svg>`;

const FEATURES = [
    {
        bg: '#f0f9ff', ic: '#0ea5e9',
        title: 'Enrollment Management',
        desc: 'Step-by-step workflows for every academic term, with status tracking and automated notifications.',
        path: 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
    },
    {
        bg: '#f5f3ff', ic: '#7c3aed',
        title: 'Smart Scheduling',
        desc: 'Auto-generate conflict-free timetables with real-time detection of room and faculty collisions.',
        path: 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5',
    },
    {
        bg: '#f0fdf4', ic: '#059669',
        title: 'Grades & Attendance',
        desc: 'Faculty record grades and mark attendance per session. Students see their records in real time.',
        path: 'M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375',
    },
    {
        bg: '#fff1f2', ic: '#e11d48',
        title: 'Role-Based Access',
        desc: 'Powered by Spatie permissions — fine-grained control over every action, per role, per module.',
        path: 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
    },
    {
        bg: '#fffbeb', ic: '#d97706',
        title: 'Assignments & Submissions',
        desc: 'Create assignments, set deadlines, and collect student submissions — all within the platform.',
        path: 'M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0H3',
    },
    {
        bg: '#eef2ff', ic: '#4338ca',
        title: 'Reports & Analytics',
        desc: 'Actionable insights on enrollment trends, academic performance, and attendance at a glance.',
        path: 'M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z',
    },
];

const STEPS = [
    {
        num: '01',
        tc: 'text-sky-700', bg: 'bg-sky-50', border: 'border-sky-200',
        title: 'Create your institution account',
        desc: 'Register and configure your school profile — set your academic year, departments, and initial structure in under 10 minutes.',
    },
    {
        num: '02',
        tc: 'text-violet-700', bg: 'bg-violet-50', border: 'border-violet-200',
        title: 'Invite faculty and enroll students',
        desc: 'Bulk-import your roster or send individual invites. Each user automatically gets a role-appropriate dashboard on first login.',
    },
    {
        num: '03',
        tc: 'text-emerald-700', bg: 'bg-emerald-50', border: 'border-emerald-200',
        title: 'Build your schedule and go live',
        desc: "Set up subjects, sections, and class schedules. The system detects conflicts before you publish — then you're ready.",
    },
];


// ─── Renderers ───────────────────────────────────────────────────────────────

function renderRolePills() {
    const container = document.getElementById('role-pills');
    if (!container) return;
    container.innerHTML = ROLE_PILLS.map(p => `
        <div class="${p.cls} role-pill">
            <span class="role-pill-dot" style="background: ${p.color};"></span>
            ${p.label}
        </div>
    `).join('');
}

function renderStats() {
    const grid = document.getElementById('stats-grid');
    if (!grid) return;
    grid.innerHTML = STATS.map((s, i) => `
        <div class="reveal" style="transition-delay: ${i * 0.1}s">
            <dt class="display-font text-4xl font-bold text-slate-900 stat-num"
                data-target="${s.target}" data-suffix="${s.suffix}">0</dt>
            <dd class="text-sm text-slate-500 mt-2 font-medium">${s.label}</dd>
            <div class="h-0.5 w-8 mx-auto mt-3 rounded-full ${s.acc}"></div>
        </div>
    `).join('');
}

function renderRoleCards() {
    const grid = document.getElementById('role-cards-grid');
    if (!grid) return;
    grid.innerHTML = ROLE_CARDS.map(card => `
        <div class="reveal rounded-2xl p-8 border border-slate-100 ${card.hoverBorder} transition-colors ${card.extraClass}"
             style="background: ${card.bg}; transition-delay: ${card.delay}">
            <div class="role-chip ${card.chipClass} mb-5">${card.chipLabel}</div>
            <h3 class="text-lg font-bold text-slate-900 mb-3">${card.title}</h3>
            <p class="text-slate-500 text-sm leading-relaxed mb-6">${card.desc}</p>
            <ul class="space-y-2.5">
                ${card.items.map(item => `
                    <li class="flex items-center gap-2.5 text-sm text-slate-600" style="color: ${card.checkColor}">
                        ${CHECK_ICON}
                        <span class="text-slate-600">${item}</span>
                    </li>
                `).join('')}
            </ul>
        </div>
    `).join('');
}

function renderFeatures() {
    const grid = document.getElementById('features-grid');
    if (!grid) return;
    grid.innerHTML = FEATURES.map((f, i) => `
        <div class="feature-card reveal bg-white rounded-2xl p-8 border border-slate-100"
             style="transition-delay: ${(i % 3) * 0.07}s">
            <div class="feature-card-inner">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5 flex-shrink-0"
                     style="background: ${f.bg};">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="${f.ic}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="${f.path}"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-900 mb-2">${f.title}</h3>
                <p class="text-sm text-slate-500 leading-relaxed">${f.desc}</p>
            </div>
        </div>
    `).join('');
}

function renderSteps() {
    const container = document.getElementById('steps-container');
    if (!container) return;
    container.innerHTML = STEPS.map((step, i) => `
        <div class="reveal flex gap-8 pb-12 relative">
            ${i < STEPS.length - 1 ? '<div class="step-line"></div>' : ''}
            <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center display-font font-bold text-sm z-10 border-2
                        ${step.tc} ${step.bg} ${step.border}">
                ${step.num}
            </div>
            <div class="pt-2.5">
                <h3 class="font-semibold text-slate-900 mb-2 text-lg">${step.title}</h3>
                <p class="text-slate-500 leading-relaxed">${step.desc}</p>
            </div>
        </div>
    `).join('');
}


// ─── Observers & interactions ─────────────────────────────────────────────────

function initReveal() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
}

function initStatCounters() {
    function animateCounter(el) {
        const target = parseInt(el.dataset.target, 10);
        const suffix = el.dataset.suffix || '';
        const steps = 1500 / 16;
        const inc = target / steps;
        let cur = 0;
        const t = setInterval(() => {
            cur = Math.min(cur + inc, target);
            el.textContent = Math.floor(cur).toLocaleString() + suffix;
            if (cur >= target) clearInterval(t);
        }, 16);
    }
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) { animateCounter(e.target); observer.unobserve(e.target); }
        });
    }, { threshold: 0.5 });
    document.querySelectorAll('[data-target]').forEach(el => observer.observe(el));
}

function initFeatureCardBeam() {
    document.querySelectorAll('.feature-card').forEach(card => {
        card.addEventListener('mousemove', e => {
            const r = card.getBoundingClientRect();
            card.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100).toFixed(1) + '%');
            card.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100).toFixed(1) + '%');
        });
    });
}

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const t = document.querySelector(a.getAttribute('href'));
            if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth' }); }
        });
    });
}


// ─── Boot ─────────────────────────────────────────────────────────────────────

renderRolePills();
renderStats();
renderRoleCards();
renderFeatures();
renderSteps();

initReveal();
initStatCounters();
initFeatureCardBeam();
initSmoothScroll();