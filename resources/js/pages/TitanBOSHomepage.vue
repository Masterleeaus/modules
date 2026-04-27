<script setup lang="ts">
import { ref } from 'vue';
import { Zap, Lock, Users, Briefcase, DollarSign, Shield, BookOpen, CheckCircle } from 'lucide-vue-next';

const expandedSection = ref<number | null>(null);
const selectedServiceMode = ref('residential');

const serviceModes = {
  residential: {
    name: 'Residential Cleaning',
    workflows: ['Pre-clean walkthrough', 'Cleaning execution', 'Post-clean inspection', 'Client sign-off'],
    compliance: ['Insurance requirements', 'Customer complaint handling', 'Safety protocols'],
    artifacts: ['Handover reports', 'Before/after galleries', 'Warranty documentation'],
  },
  bond: {
    name: 'Bond Cleaning',
    workflows: ['Inventory check', 'Deep clean phases', 'Bond schedule inspection', 'Compliance documentation'],
    compliance: ['Bond schedule adherence', 'Real estate handoff requirements', 'Dispute resolution evidence'],
    artifacts: ['Bond packs', 'Inspection reports', 'Real estate agent summaries'],
  },
  pressure: {
    name: 'Pressure Washing',
    workflows: ['Surface assessment', 'Equipment selection', 'Pressure application', 'Before/after proof'],
    compliance: ['Damage risk assessment', 'Surface-specific protocols', 'Equipment safety checks'],
    artifacts: ['Damage reports', 'Surface assessments', 'Service warranties'],
  },
  solar: {
    name: 'Solar Panel Cleaning',
    workflows: ['Safety harness verification', 'Water quality checks', 'Panel cleaning', 'Performance verification'],
    compliance: ['Electrical safety', 'Roof access permits', 'Warranty protection'],
    artifacts: ['Performance reports', 'Safety certifications', 'Maintenance logs'],
  },
};

const apps = [
  { tier: 'FIELD EXECUTION', name: 'Titan Go', icon: '📱', category: 'Guided Work Engine', owns: 'work correctness', tagline: 'Field Execution Operating System', url: '/apps/titan-go', features: ['Context-aware checklist mutation adapts to site, weather and inspection risk', 'Evidence confidence engine requests retakes automatically', 'Technician skill matching hides unauthorised tasks', 'Offline-first evidence chain creates legal-grade documentation', 'Completion confidence score predicts inspection pass probability'], impact: '2–3 more hours cleaning. Zero dispute callbacks.' },
  { tier: 'OPERATIONS', name: 'Ground Zero', icon: '🎯', category: 'Stage-Aware Dispatch System', owns: 'execution timing', tagline: 'Lifecycle Orchestration Engine', url: '/apps/ground-zero', features: ['Lifecycle graph scheduling plans job states, not just slots', 'Constraint-aware dispatch AI optimises travel, equipment, weather and SLA', 'Zone-based work mapping for construction and large-site cleaning', 'Delay cascade simulation predicts downstream impacts', 'Dispatch risk meter warns before failures occur'], impact: 'More jobs per tech. Fewer domino failures. Higher SLA compliance.' },
  { tier: 'CONTRACT', name: 'Titan Pro', icon: '📊', category: 'Service Performance Command System', owns: 'decision visibility', tagline: 'Operational Intelligence Layer', url: '/apps/titan-pro', features: ['Intervention radar flags likely failures before they happen', 'Margin heatmap by service type reveals profit and leakage', 'Compliance exposure index tracks certification gaps and audit risk', 'Client risk forecasting predicts churn and complaints', 'Workforce load stability score detects burnout patterns'], impact: 'Spot problem sites early. Prevent revenue leakage. Drive strategy.' },
  { tier: 'SOLO', name: 'Titan Solo', icon: '👤', category: 'Operator Autopilot System', owns: 'operator stability', tagline: 'Independent Operator OS', url: '/apps/titan-solo', features: ['Daily route optimiser plans travel, breaks and supply stops', 'Income stability predictor forecasts weekly variance', 'Smart repeat booking engine detects service renewal moments', 'One-tap completion accounting sends invoice, payment session and follow-up', 'Capacity expansion advisor indicates when to hire first staff'], impact: 'Less admin burden. Predictable recurring revenue. Clear growth path.' },
  { tier: 'COMMS', name: 'Titan Hello', icon: '💬', category: 'Conversational Operations Gateway', owns: 'service entry', tagline: 'Autonomous Service Front Door', url: '/apps/titan-hello', features: ['Intent-to-job engine detects quotes, urgent work, renewals and complaints', 'Multi-channel conversation memory keeps SMS, email, portal and voice together', 'Smart scope builder builds property profile before scheduling', 'Booking confidence scoring flags inspection risk and complexity', 'Pre-dispatch readiness validates access, pets, parking and expectations'], impact: '15–20% higher rebooking. Fewer field surprises. Better data quality.' },
  { tier: 'AI', name: 'Titan Zero', icon: '🧠', category: 'Operational Knowledge Infrastructure', owns: 'institutional intelligence', tagline: 'Service-Mode Intelligence Engine', url: '/apps/titan-zero', features: ['Procedural memory remembers how your company works', 'Inspection strategy advisor predicts what inspectors check', 'Training gap detector identifies skill gaps preemptively', 'Artefact auto-composer turns photos, checklists and notes into bond packs', 'Policy translator turns standards into executable checklist logic'], impact: 'Faster onboarding. Fewer compliance mistakes. Institutional knowledge stays.' },
  { tier: 'PAY', name: 'ZeroPay', icon: '💳', category: 'Payment Operations Layer', owns: 'revenue completion', tagline: 'Revenue Completion Engine', url: '/apps/zero-pay', features: ['Smart payment sessions for invoices, deposits and balances', 'QR codes and payment links across email, SMS, WhatsApp, portal and print', 'Zero-fee-first rails promote PayID, bank transfer and cash', 'Payment attempt tracking shows opens, choices, failures and review queues', 'AI follow-up and optional voice recovery improve collection rates'], impact: 'Instant on-site collection. Better cash flow. Higher margins.' },
  { tier: 'PROPERTY', name: 'Zero Fuss', icon: '🔑', category: 'Service Relationship Automation Engine', owns: 'trust continuity', tagline: 'Client Experience Layer', url: '/apps/zero-fuss', features: ['Expectation alignment confirms arrival, outcome and access rules', 'Readiness confirmation gives proactive property updates', 'Evidence delivery streams show live completion timelines', 'Satisfaction prediction triggers recovery before complaints arrive', 'Silent approval detection reduces unnecessary follow-ups'], impact: 'More PM referrals. Smoother renewals. Fewer disputes.' },
  { tier: 'TRAIN', name: 'Titan Studio', icon: '📚', category: 'Service Growth Engine', owns: 'demand creation', tagline: 'Training and Demand Generator', url: '/apps/titan-studio', features: ['Service gap detection identifies undersupplied local niches', 'Seasonality opportunity engine schedules campaigns around demand', 'Reputation flywheel automation asks for reviews at the right moment', 'Upsell detection suggests adjacent services', 'Local authority positioning creates niche-specific campaigns'], impact: 'Built-in growth engine. Consistent quality. Predictable demand.' },
];

const zeroPillars = [
  { icon: DollarSign, title: 'Zero Hidden Pricing', problem: 'SaaS tools nickel-and-dime with per-user seats, per-API charges and surprise overages.', solution: 'Transparent subscription-first pricing. No token traps. No forced add-ons. Scale without spiralling costs.', outcome: 'You know your software cost from day one.' },
  { icon: Lock, title: 'Zero AI Lock-In', problem: 'Locked into a vendor’s expensive AI provider, paying markup plus subscription.', solution: 'Bring your own API keys. Use any provider or local models. Or do not use AI at all.', outcome: 'You control costs and own the relationship with your AI provider.' },
  { icon: Shield, title: 'Zero Data Resale', problem: 'Vendors monetize your data, sell insights, train AI on jobs, or license patterns.', solution: 'Your data is yours. Titan does not train on it, sell insights, or harvest your operations.', outcome: 'Your competitive advantage stays yours.' },
  { icon: Zap, title: 'Zero Workflow Duplication', problem: 'Fragmented tools force staff to retype jobs across multiple apps.', solution: 'One operational record. Every app reads and writes to the same business truth.', outcome: 'Technicians clean instead of retyping. Real-time truth everywhere.' },
  { icon: Users, title: 'Zero Learning Curve', problem: 'Generic terminology forces weeks of training and change management.', solution: 'Service-mode terminology, checklists, and training load for your niche.', outcome: 'Staff use it faster because the interface speaks their language.' },
  { icon: Briefcase, title: 'Zero Platform Dependency', problem: 'Vendor lock-in traps your workflows and data inside a black box.', solution: 'Titan orchestrates. Your processes remain portable. You control your destiny.', outcome: 'Titan serves you, not the other way around.' },
];
</script>

<template>
  <div class="bg-black text-white font-sans overflow-hidden">
    <section class="min-h-screen bg-gradient-to-br from-black via-slate-900 to-black px-6 md:px-12 py-20 flex items-center justify-center relative overflow-hidden">
      <div class="absolute inset-0 opacity-20">
        <div class="absolute top-20 left-10 w-72 h-72 bg-blue-500 rounded-full mix-blend-screen filter blur-3xl opacity-10"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-cyan-500 rounded-full mix-blend-screen filter blur-3xl opacity-10"></div>
      </div>
      <div class="relative z-10 max-w-5xl text-center">
        <h1 class="text-7xl md:text-8xl font-black mb-6 leading-tight"><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-cyan-300 to-blue-500">Titan BOS</span><br><span class="text-white">Zero BS.</span></h1>
        <p class="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto leading-relaxed">The only AI operating system built exclusively for cleaning businesses. Titan runs the business. Zero removes the friction across scheduling, compliance, training, evidence, payments, and the thousands of decisions that drain operator time.</p>
        <div class="flex gap-4 justify-center flex-wrap"><a href="/register" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition">Start Your Titan System</a><a href="/apps" class="px-8 py-4 border-2 border-blue-400 text-blue-300 hover:bg-blue-400/10 font-bold rounded-lg transition">Explore the Apps</a></div>
      </div>
    </section>

    <section class="px-6 md:px-12 py-24 bg-slate-950">
      <div class="max-w-7xl mx-auto"><h2 class="text-5xl md:text-6xl font-black mb-4 text-center"><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">The Zero Philosophy</span></h2><p class="text-center text-gray-400 text-lg mb-16">Not branding decoration. Real operating doctrine.</p>
        <div class="grid md:grid-cols-2 gap-8">
          <div v-for="pillar in zeroPillars" :key="pillar.title" class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-lg p-8 border border-slate-700 hover:border-blue-500/50 transition hover:shadow-lg hover:shadow-blue-500/20">
            <div class="flex items-center gap-4 mb-4"><component :is="pillar.icon" class="w-6 h-6 text-blue-400" /><h3 class="text-xl font-bold">{{ pillar.title }}</h3></div>
            <div class="space-y-4 text-sm"><div><p class="text-gray-500 font-semibold mb-1">THE PROBLEM</p><p class="text-gray-300">{{ pillar.problem }}</p></div><div><p class="text-gray-500 font-semibold mb-1">TITAN SOLUTION</p><p class="text-gray-300">{{ pillar.solution }}</p></div><div class="pt-2 border-t border-slate-700"><p class="text-blue-300 font-semibold italic">{{ pillar.outcome }}</p></div></div>
          </div>
        </div>
      </div>
    </section>

    <section class="px-6 md:px-12 py-24 bg-black">
      <div class="max-w-7xl mx-auto"><h2 class="text-5xl md:text-6xl font-black mb-16 text-center"><span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">Super Niche AI</span><br><span class="text-white">for Cleaning Operators</span></h2>
        <div class="flex gap-4 justify-center mb-12 flex-wrap"><button v-for="(data, key) in serviceModes" :key="key" @click="selectedServiceMode = String(key)" :class="selectedServiceMode === key ? 'bg-blue-600 text-white' : 'bg-slate-800 text-gray-300 hover:bg-slate-700'" class="px-6 py-3 rounded-lg font-semibold transition">{{ data.name }}</button></div>
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl p-12 border border-slate-700"><h3 class="text-3xl font-bold mb-8 text-cyan-300">{{ serviceModes[selectedServiceMode].name }}</h3>
          <div class="grid md:grid-cols-3 gap-8">
            <div><h4 class="text-sm font-bold text-gray-400 mb-4 uppercase tracking-wide">Niche Workflows</h4><ul class="space-y-3"><li v-for="item in serviceModes[selectedServiceMode].workflows" :key="item" class="flex items-start gap-3"><CheckCircle class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" /><span class="text-gray-200">{{ item }}</span></li></ul></div>
            <div><h4 class="text-sm font-bold text-gray-400 mb-4 uppercase tracking-wide">Compliance Logic</h4><ul class="space-y-3"><li v-for="item in serviceModes[selectedServiceMode].compliance" :key="item" class="flex items-start gap-3"><Shield class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" /><span class="text-gray-200">{{ item }}</span></li></ul></div>
            <div><h4 class="text-sm font-bold text-gray-400 mb-4 uppercase tracking-wide">Auto-Generated Artefacts</h4><ul class="space-y-3"><li v-for="item in serviceModes[selectedServiceMode].artifacts" :key="item" class="flex items-start gap-3"><BookOpen class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" /><span class="text-gray-200">{{ item }}</span></li></ul></div>
          </div>
        </div>
      </div>
    </section>

    <section class="px-6 md:px-12 py-24 bg-slate-950"><div class="max-w-7xl mx-auto"><h2 class="text-5xl md:text-6xl font-black mb-4 text-center"><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">Up to Nine Apps</span></h2><p class="text-center text-gray-400 text-lg mb-16">One cleaning business operating system.</p>
      <div class="grid md:grid-cols-3 gap-6"><div v-for="(app, idx) in apps" :key="app.name" class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-lg p-6 border border-slate-700 hover:border-blue-500/50 transition cursor-pointer group" @click="expandedSection = expandedSection === idx ? null : idx">
        <div class="text-4xl mb-3">{{ app.icon }}</div><p class="text-xs font-bold text-blue-400 mb-1 uppercase tracking-wider">{{ app.tier }}</p><h3 class="text-2xl font-bold mb-1 group-hover:text-blue-300 transition">{{ app.name }}</h3><p class="text-xs text-cyan-300 font-semibold mb-3 italic">{{ app.category }}</p><p class="text-sm text-gray-400 mb-4">{{ app.tagline }}</p><div class="text-xs bg-slate-700/50 rounded px-3 py-2 mb-4 text-gray-300"><span class="font-bold text-blue-300">Owns: </span>{{ app.owns }}</div>
        <div v-if="expandedSection === idx" class="mt-6 pt-6 border-t border-slate-700 space-y-4"><div><p class="text-xs font-bold text-gray-500 mb-2">CONTROL PLANE</p><ul class="space-y-2"><li v-for="f in app.features" :key="f" class="text-sm text-gray-300 flex items-start gap-2"><span class="w-1.5 h-1.5 bg-blue-400 rounded-full mt-1.5 flex-shrink-0"></span><span>{{ f }}</span></li></ul></div><div class="pt-4 border-t border-slate-700"><p class="text-sm font-semibold text-cyan-300">→ {{ app.impact }}</p><a :href="app.url" class="mt-3 inline-flex text-sm font-bold text-blue-300 hover:text-blue-200">Open app page →</a></div></div>
      </div></div>
    </div></section>

    <section class="px-6 md:px-12 py-24 bg-black"><div class="max-w-5xl mx-auto text-center"><h2 class="text-5xl md:text-6xl font-black mb-12"><span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-500">Hello Receives. Zero Thinks. Titan Executes.</span></h2><div class="space-y-6 text-lg text-gray-300"><p><span class="text-blue-400 font-bold">Hello Receives:</span> Customer books a job. Titan Hello sends confirmation and arrival notice.</p><p><span class="text-cyan-400 font-bold">Zero Thinks:</span> AI analyzes the job and auto-generates checklists, SWMS, training flows, or artefacts as needed.</p><p><span class="text-blue-400 font-bold">Titan Executes:</span> Titan Go loads the job. The technician executes. Ground Zero tracks progress. Titan Pro inspects quality. ZeroPay collects payment.</p></div><p class="mt-12 text-gray-500 italic">Everything connects. One business. One system. No duplication.</p></div></section>

    <section class="px-6 md:px-12 py-24 bg-gradient-to-r from-blue-900/20 to-cyan-900/20 border-t border-slate-700"><div class="max-w-4xl mx-auto text-center"><h2 class="text-4xl font-black mb-8">Ready to remove the BS from your cleaning operation?</h2><a href="/register" class="inline-flex px-10 py-5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-lg transition shadow-lg shadow-blue-600/50">Launch Titan Zero — Start Free Trial</a><p class="text-gray-400 mt-6 text-sm">One Service Mode. One system. Zero BS.</p></div></section>
    <footer class="px-6 md:px-12 py-12 bg-black border-t border-slate-800"><div class="max-w-7xl mx-auto text-center text-gray-500 text-sm"><p>Titan BOS — The operating system for cleaning businesses. Titan Capability. Zero BS.</p></div></footer>
  </div>
</template>
