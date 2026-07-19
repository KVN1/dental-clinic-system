<x-clinic-layout :title="'User Manual'">

<div x-data="{ activeSection: 'getting-started' }">

    <!-- Sticky in-page nav -->
    <div class="panel" style="margin-bottom:1.25rem;position:sticky;top:0;z-index:10;">
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button type="button" @click="activeSection = 'getting-started'; $el.scrollIntoView" :class="activeSection === 'getting-started' ? 'manual-nav-btn active' : 'manual-nav-btn'" onclick="document.getElementById('sec-getting-started').scrollIntoView({behavior:'smooth', block:'start'})">Getting Started</button>
            <button type="button" class="manual-nav-btn" onclick="document.getElementById('sec-patients').scrollIntoView({behavior:'smooth', block:'start'})">Patients</button>
            <button type="button" class="manual-nav-btn" onclick="document.getElementById('sec-appointments').scrollIntoView({behavior:'smooth', block:'start'})">Appointments</button>
            <button type="button" class="manual-nav-btn" onclick="document.getElementById('sec-billing').scrollIntoView({behavior:'smooth', block:'start'})">Billing &amp; Payments</button>
            <button type="button" class="manual-nav-btn" onclick="document.getElementById('sec-prescriptions').scrollIntoView({behavior:'smooth', block:'start'})">Prescriptions</button>
            <button type="button" class="manual-nav-btn" onclick="document.getElementById('sec-images').scrollIntoView({behavior:'smooth', block:'start'})">X-Rays &amp; Images</button>
            <button type="button" class="manual-nav-btn" onclick="document.getElementById('sec-reports').scrollIntoView({behavior:'smooth', block:'start'})">Reports</button>
            <button type="button" class="manual-nav-btn" onclick="document.getElementById('sec-settings').scrollIntoView({behavior:'smooth', block:'start'})">Settings</button>
        </div>
    </div>

    <!-- ===== GETTING STARTED ===== -->
    <div class="panel manual-section" id="sec-getting-started">
        <h2 class="panel-title" style="margin-bottom:1rem;">Getting Started</h2>
        <p class="manual-intro">Welcome! This guide walks you through everything you need to run your clinic day-to-day. Use the buttons above to jump to any section.</p>

        <div class="manual-step">
            <div class="manual-step-num">1</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Logging In</div>
                <div class="manual-step-desc">Enter your email and password on the login screen, then click Sign In. If you forgot your password, use the "Forgot Password" link.</div>
                <div class="manual-visual">
                    <div class="mv-window">
                        <div class="mv-titlebar">Sign In</div>
                        <div class="mv-body">
                            <div class="mv-field"><span class="mv-label">Email</span><div class="mv-input">you@clinic.com</div></div>
                            <div class="mv-field"><span class="mv-label">Password</span><div class="mv-input">••••••••</div></div>
                            <div class="mv-btn">Sign In</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">2</div>
            <div class="manual-step-content">
                <div class="manual-step-title">The Sidebar</div>
                <div class="manual-step-desc">Everything is one click away from the sidebar on the left: Dashboard, Patients, Appointments, Logs, and Settings. The Reminders bell shows a number badge when something needs your attention.</div>
                <div class="manual-visual">
                    <div class="mv-sidebar-demo">
                        <div class="mv-side-item active">▦ Dashboard</div>
                        <div class="mv-side-item">◍ Patients</div>
                        <div class="mv-side-item">▤ Appointments</div>
                        <div class="mv-side-item">! Reminders <span class="mv-badge">3</span></div>
                        <div class="mv-side-item">⚙ Settings</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">3</div>
            <div class="manual-step-content">
                <div class="manual-step-title">The Dashboard</div>
                <div class="manual-step-desc">Your homepage after logging in. Shows total patients, outstanding balance, today's appointments, and a Reminders panel with this week's schedule, unpaid balances, and prescriptions that may need follow-up.</div>
            </div>
        </div>
    </div>

    <!-- ===== PATIENTS ===== -->
    <div class="panel manual-section" id="sec-patients">
        <h2 class="panel-title" style="margin-bottom:1rem;">Managing Patients</h2>

        <div class="manual-step">
            <div class="manual-step-num">1</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Adding a New Patient</div>
                <div class="manual-step-desc">Go to Patients &rarr; click "+ Add Patient". Fill in their basic info, medical history, and check off any relevant conditions from the checklist. Only Name is required, everything else can be filled in later.</div>
                <div class="manual-visual">
                    <div class="mv-btn-demo">+ Add Patient</div>
                </div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">2</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Searching, Sorting &amp; Filtering</div>
                <div class="manual-step-desc">Use the search bar to find a patient instantly by name, contact number, or email. Use the Sort By dropdown to order the list (A-Z, newest added, highest balance, etc.), and the filter dropdowns to narrow down by balance status or upcoming appointments.</div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">3</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Logging a Visit</div>
                <div class="manual-step-desc">Open a patient's profile &rarr; scroll to "Add Entry" &rarr; choose Visit/Procedure, Payment, or Note. Fill in the tooth number, description, and any amount charged or paid, then save. This updates their balance automatically.</div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">4</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Printing Records</div>
                <div class="manual-step-desc">From a patient's profile, use the Print Receipt or Print Full Record buttons to generate a clean, printable document with your clinic's branding.</div>
            </div>
        </div>
    </div>

    <!-- ===== APPOINTMENTS ===== -->
    <div class="panel manual-section" id="sec-appointments">
        <h2 class="panel-title" style="margin-bottom:1rem;">Appointments</h2>

        <div class="manual-step">
            <div class="manual-step-num">1</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Booking an Appointment</div>
                <div class="manual-step-desc">Go to Appointments &rarr; click "+ Book Appointment", or click any date on the calendar view at the bottom of the page to book directly for that day.</div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">2</div>
            <div class="manual-step-content">
                <div class="manual-step-title">The Calendar View</div>
                <div class="manual-step-desc">Scroll down on the Appointments page to see a full month calendar. Each dot represents an appointment, color-coded by dentist. Click any day to see details or book a new appointment for that date.</div>
                <div class="manual-visual">
                    <div class="mv-calendar-demo">
                        <div class="mv-cal-header">
                            <span>&larr;</span><span>July 2026</span><span>&rarr;</span>
                        </div>
                        <div class="mv-cal-grid">
                            @for($i = 1; $i <= 14; $i++)
                                <div class="mv-cal-cell {{ $i == 8 ? 'has-appt' : '' }}">{{ $i }}</div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">3</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Rescheduling</div>
                <div class="manual-step-desc">Open the appointment &rarr; change the date or time &rarr; optionally add a reason (e.g. "Patient requested") &rarr; save. The system keeps a full history and shows how many times it's been rescheduled.</div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">4</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Assigning a Dentist</div>
                <div class="manual-step-desc">If your clinic has multiple dentists, choose one from the Dentist dropdown when booking. This color-codes the appointment on the calendar and filters the schedule per dentist.</div>
            </div>
        </div>
    </div>

    <!-- ===== BILLING ===== -->
    <div class="panel manual-section" id="sec-billing">
        <h2 class="panel-title" style="margin-bottom:1rem;">Billing &amp; Payments</h2>

        <div class="manual-step">
            <div class="manual-step-num">1</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Recording a Payment</div>
                <div class="manual-step-desc">From a patient's profile, add a new entry with type "Payment". Choose the payment method (Cash, GCash, Bank Transfer, etc.) and enter the amount. Their balance updates automatically.</div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">2</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Setting Up Payment Methods</div>
                <div class="manual-step-desc">Go to Settings &rarr; Clinic &rarr; Billing &amp; Currency to choose which payment methods appear as options, and set your clinic's currency and tax rate.</div>
            </div>
        </div>
    </div>

    <!-- ===== PRESCRIPTIONS ===== -->
    <div class="panel manual-section" id="sec-prescriptions">
        <h2 class="panel-title" style="margin-bottom:1rem;">Prescriptions</h2>

        <div class="manual-step">
            <div class="manual-step-num">1</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Writing a Prescription</div>
                <div class="manual-step-desc">From a patient's profile, click "+ New Prescription". Add one or more medications with dosage, frequency, and duration. Click "+ Add Another Medication" to add more than one item to the same prescription.</div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">2</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Printing a Prescription</div>
                <div class="manual-step-desc">Click "Print" next to any saved prescription to generate a professional Rx slip with your clinic's branding and a signature line for the dentist.</div>
            </div>
        </div>
    </div>

    <!-- ===== IMAGES ===== -->
    <div class="panel manual-section" id="sec-images">
        <h2 class="panel-title" style="margin-bottom:1rem;">X-Rays &amp; Images</h2>

        <div class="manual-step">
            <div class="manual-step-num">1</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Uploading Images</div>
                <div class="manual-step-desc">On a patient's profile, click "+ Upload Image". Choose the type (X-Ray, Clinical Photo, Document, Other), optionally add a label, and select one or more files. You'll see a preview before saving.</div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">2</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Viewing Images</div>
                <div class="manual-step-desc">Click any thumbnail to open it full-screen. Click the X or anywhere outside the image to close it.</div>
            </div>
        </div>
    </div>

    <!-- ===== REPORTS ===== -->
    <div class="panel manual-section" id="sec-reports">
        <h2 class="panel-title" style="margin-bottom:1rem;">Reports</h2>

        <div class="manual-step">
            <div class="manual-step-num">1</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Monthly Revenue Report</div>
                <div class="manual-step-desc">Found under Reports in the sidebar (admin only). Shows total collected, uncollected balance, a breakdown by procedure, and every entry for the selected month. Printable.</div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">2</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Appointment Summary Report</div>
                <div class="manual-step-desc">Shows appointments grouped by day for any date range, with counts for scheduled, completed, no-show, and cancelled appointments.</div>
            </div>
        </div>
    </div>

    <!-- ===== SETTINGS ===== -->
    <div class="panel manual-section" id="sec-settings">
        <h2 class="panel-title" style="margin-bottom:1rem;">Settings</h2>

        <div class="manual-step">
            <div class="manual-step-num">1</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Clinic Branding</div>
                <div class="manual-step-desc">Go to Settings &rarr; Clinic tab. Update your clinic name, logo, tagline, contact info, currency, and colors. Changes apply instantly across the whole system, including the login page.</div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">2</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Theme &amp; Colors</div>
                <div class="manual-step-desc">Pick from ready-made color presets, or set your own Primary, Secondary, Background, and Surface colors. The live preview shows exactly how it will look before you save. Text colors automatically adjust to stay readable.</div>
                <div class="manual-visual">
                    <div style="display:flex;gap:8px;">
                        <div class="mv-color-swatch" style="background:#2A9D8F;"></div>
                        <div class="mv-color-swatch" style="background:#FF8966;"></div>
                        <div class="mv-color-swatch" style="background:#1e4a8a;"></div>
                        <div class="mv-color-swatch" style="background:#6d28d9;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">3</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Adding Staff &amp; Dentists</div>
                <div class="manual-step-desc">Go to Settings &rarr; Staff Accounts. Create accounts for Staff, Dentists, or Admins. Dentist accounts can have a specialty and calendar color for scheduling.</div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">4</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Backups</div>
                <div class="manual-step-desc">Go to Settings &rarr; Backups tab. Click "Create Backup" to save a snapshot of your data. Set up automatic daily or hourly backups, and optionally save copies to an external folder.</div>
            </div>
        </div>

        <div class="manual-step">
            <div class="manual-step-num">5</div>
            <div class="manual-step-content">
                <div class="manual-step-title">Exporting Data</div>
                <div class="manual-step-desc">Go to Settings &rarr; Exports tab to download your patient list or visit logs as a spreadsheet (CSV) file.</div>
            </div>
        </div>
    </div>

</div>

</x-clinic-layout>