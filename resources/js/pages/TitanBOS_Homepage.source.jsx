import React, { useState } from 'react';
import { ChevronDown, Zap, Lock, Users, Briefcase, CheckCircle, DollarSign, Shield, BookOpen } from 'lucide-react';

export default function TitanBOSHomepage() {
  const [expandedSection, setExpandedSection] = useState(null);
  const [selectedVertical, setSelectedVertical] = useState('residential');

  const verticals = {
    residential: {
      name: 'Residential Cleaning',
      workflows: ['Pre-clean walkthrough', 'Cleaning execution', 'Post-clean inspection', 'Client sign-off'],
      compliance: ['Insurance requirements', 'Customer complaint handling', 'Safety protocols'],
      artifacts: ['Handover reports', 'Before/after galleries', 'Warranty documentation']
    },
    bond: {
      name: 'Bond Cleaning',
      workflows: ['Inventory check', 'Deep clean phases', 'Bond schedule inspection', 'Compliance documentation'],
      compliance: ['Bond schedule adherence', 'Real estate handoff requirements', 'Dispute resolution evidence'],
      artifacts: ['Bond packs', 'Inspection reports', 'Real estate agent summaries']
    },
    pressure: {
      name: 'Pressure Washing',
      workflows: ['Surface assessment', 'Equipment selection', 'Pressure application', 'Before/after proof'],
      compliance: ['Damage risk assessment', 'Surface-specific protocols', 'Equipment safety checks'],
      artifacts: ['Damage reports', 'Surface assessments', 'Service warranties']
    },
    solar: {
      name: 'Solar Panel Cleaning',
      workflows: ['Safety harness verification', 'Water quality checks', 'Panel cleaning', 'Performance verification'],
      compliance: ['Electrical safety', 'Roof access permits', 'Warranty protection'],
      artifacts: ['Performance reports', 'Safety certifications', 'Maintenance logs']
    }
  };

  const apps = [
    {
      tier: 'FIELD EXECUTION',
      name: 'Titan Go',
      icon: '📱',
      category: 'Guided Work Engine',
      owns: 'work correctness',
      tagline: 'Field Execution Operating System',
      features: ['Context-aware checklist mutation (adapts to site, weather, risk)', 'Evidence confidence engine (requests retakes automatically)', 'Technician skill matching (hides unauthorized tasks)', 'Offline-first evidence chain (legal-grade documentation)', 'Completion confidence score (predicts inspection pass)'],
      impact: '2–3 more hours cleaning. Zero dispute callbacks.'
    },
    {
      tier: 'OPERATIONS',
      name: 'Ground Zero',
      icon: '🎯',
      category: 'Stage-Aware Dispatch System',
      owns: 'execution timing',
      tagline: 'Lifecycle Orchestration Engine',
      features: ['Lifecycle graph scheduling (schedules states, not slots)', 'Constraint-aware dispatch AI (multi-variable optimization)', 'Zone-based work mapping (spatial dispatch for construction)', 'Delay cascade simulation (predicts downstream impacts)', 'Dispatch risk meter (warns of failures before they occur)'],
      impact: 'More jobs per tech. Fewer domino failures. Higher SLA compliance.'
    },
    {
      tier: 'CONTRACT',
      name: 'Titan Pro',
      icon: '📊',
      category: 'Service Performance Command System',
      owns: 'decision visibility',
      tagline: 'Operational Intelligence Layer',
      features: ['Intervention radar (alerts on failures before they occur)', 'Margin heatmap by service type (immediate profitability insight)', 'Compliance exposure index (certification gaps, audit risk)', 'Client risk forecasting (churn, complaints, late payments)', 'Workforce load stability score (burnout pattern detection)'],
      impact: 'Spot problem sites early. Prevent revenue leakage. Drive strategy.'
    },
    {
      tier: 'SOLO',
      name: 'Titan Solo',
      icon: '👤',
      category: 'Operator Autopilot System',
      owns: 'operator stability',
      tagline: 'Independent Operator OS',
      features: ['Daily route optimizer (travel, job order, breaks, supplies)', 'Income stability predictor (forecasts revenue variance)', 'Smart repeat booking engine (auto-detects renewal opportunities)', 'One-tap completion accounting (invoice+payment+follow-up)', 'Capacity expansion advisor (tells when to hire first staff)'],
      impact: 'Less admin burden. Predictable recurring revenue. Clear growth path.'
    },
    {
      tier: 'COMMS',
      name: 'Titan Hello',
      icon: '💬',
      category: 'Conversational Operations Gateway',
      owns: 'service entry',
      tagline: 'Autonomous Service Front Door',
      features: ['Intent-to-job engine (detects quote, urgent, inspection, renewal, complaint)', 'Multi-channel conversation memory (SMS→email→portal→voice continuity)', 'Smart scope builder (builds property profile before scheduling)', 'Booking confidence scoring (flags inspection risk, complexity)', 'Pre-dispatch readiness validation (confirms access before dispatch)'],
      impact: '15–20% higher rebooking. Fewer field surprises. Better data quality.'
    },
    {
      tier: 'AI',
      name: 'Titan Zero',
      icon: '🧠',
      category: 'Operational Knowledge Infrastructure',
      owns: 'institutional intelligence',
      tagline: 'Vertical Intelligence Engine',
      features: ['Procedural memory layer (remembers how your company works)', 'Inspection strategy advisor (predicts what inspectors check)', 'Training gap detector (identifies skill gaps preemptively)', 'Artefact auto-composer (photos+checklist+notes→bond pack)', 'Policy translator (ISO guideline→executable checklist logic)'],
      impact: 'Faster onboarding. Fewer compliance mistakes. Institutional knowledge stays.'
    },
    {
      tier: 'PAY',
      name: 'ZeroPay',
      icon: '💳',
      category: 'Payment Operations Layer',
      owns: 'revenue completion',
      tagline: 'Revenue Completion Engine',
      features: ['Payment sessions (secure, PCI-compliant, device-first)', 'QR code payments (no card reader required)', 'Payment links (text-based payment, zero friction)', 'PayID and bank transfer options (card-free payments)', 'Zero processing fees (margin protection)'],
      impact: 'Instant on-site collection. Better cash flow. Higher margins.'
    },
    {
      tier: 'PROPERTY',
      name: 'Zero Fuss',
      icon: '🔑',
      category: 'Service Relationship Automation Engine',
      owns: 'trust continuity',
      tagline: 'Client Experience Layer',
      features: ['Expectation alignment engine (confirms arrival, outcome, access)', 'Readiness confirmation layer (proactive property staging updates)', 'Evidence delivery streams (live timeline instead of attachments)', 'Satisfaction prediction engine (detects dissatisfaction early)', 'Silent approval detection (learns client acceptance patterns)'],
      impact: 'More PM referrals. Smoother renewals. Fewer disputes.'
    },
    {
      tier: 'TRAIN',
      name: 'Titan Studio',
      icon: '📚',
      category: 'Vertical-Aware Demand Generator',
      owns: 'demand creation',
      tagline: 'Service Growth Engine',
      features: ['Service gap detection (identifies undersupplied local verticals)', 'Seasonality opportunity engine (auto-triggers campaign windows)', 'Reputation flywheel automation (optimal review request timing)', 'Upsell opportunity detection (suggests adjacent services)', 'Local authority positioning engine (builds inspection-ready brand)'],
      impact: 'Built-in growth engine. Consistent quality. Predictable demand.'
    }
  ];

  const zeroPillars = [
    {
      icon: <DollarSign className="w-6 h-6" />,
      title: 'Zero Hidden Pricing',
      problem: 'SaaS tools nickel-and-dime with per-user seats, per-API charges, surprise overages',
      solution: 'Transparent subscription-first. No token traps. No forced add-ons. Scale without spiraling costs.',
      outcome: 'You know your software cost from day one.'
    },
    {
      icon: <Lock className="w-6 h-6" />,
      title: 'Zero AI Lock-In',
      problem: 'Locked into vendor\'s expensive AI provider, paying markup + subscription',
      solution: 'Bring your own API keys. Use any provider or local models. Or don\'t use AI at all.',
      outcome: 'You control costs and own the relationship with your AI provider.'
    },
    {
      icon: <Shield className="w-6 h-6" />,
      title: 'Zero Data Resale',
      problem: 'Vendors monetize your data: selling insights, training AI on your jobs, licensing patterns',
      solution: 'Your data is yours. We don\'t train on it. We don\'t sell insights. We don\'t harvest.',
      outcome: 'Your competitive advantage stays yours.'
    },
    {
      icon: <Zap className="w-6 h-6" />,
      title: 'Zero Workflow Duplication',
      problem: 'Fragmented tools force retyping jobs across three separate apps (5–10 hrs/week)',
      solution: 'One operational record. Every app reads and writes to the same database.',
      outcome: 'Technicians clean instead of retype. Real-time truth everywhere.'
    },
    {
      icon: <Users className="w-6 h-6" />,
      title: 'Zero Learning Curve',
      problem: 'Generic terminology ("task," "project") requires weeks of learning',
      solution: 'Vertical terminology, checklists, and training load for your niche. Day-one adoption.',
      outcome: 'Staff use it without formal training. Interface speaks their language.'
    },
    {
      icon: <Briefcase className="w-6 h-6" />,
      title: 'Zero Platform Dependency',
      problem: 'Vendor lock-in traps your workflows and data inside a black box',
      solution: 'Titan orchestrates. Your processes remain portable. You control your destiny.',
      outcome: 'You\'re in control. Titan serves you, not the other way around.'
    }
  ];

  return (
    <div className="bg-black text-white font-sans overflow-hidden">
      {/* HERO */}
      <section className="min-h-screen bg-gradient-to-br from-black via-slate-900 to-black px-6 md:px-12 py-20 flex items-center justify-center relative overflow-hidden">
        <div className="absolute inset-0 opacity-20">
          <div className="absolute top-20 left-10 w-72 h-72 bg-blue-500 rounded-full mix-blend-screen filter blur-3xl opacity-10"></div>
          <div className="absolute bottom-20 right-10 w-96 h-96 bg-cyan-500 rounded-full mix-blend-screen filter blur-3xl opacity-10"></div>
        </div>
        <div className="relative z-10 max-w-5xl text-center">
          <h1 className="text-7xl md:text-8xl font-black mb-6 leading-tight">
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-cyan-300 to-blue-500">Titan BOS</span>
            <br />
            <span className="text-white">Zero BS.</span>
          </h1>
          <p className="text-xl md:text-2xl text-gray-300 mb-8 max-w-3xl mx-auto leading-relaxed">
            The only AI operating system built exclusively for cleaning businesses. Titan runs the business. Zero removes the friction across scheduling, compliance, training, evidence, payments, and the thousands of decisions that drain operator time.
          </p>
          <div className="flex gap-4 justify-center flex-wrap">
            <button className="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition">
              Start Your Titan System
            </button>
            <button className="px-8 py-4 border-2 border-blue-400 text-blue-300 hover:bg-blue-400/10 font-bold rounded-lg transition">
              Explore the Apps
            </button>
          </div>
        </div>
      </section>

      {/* THE ZERO PHILOSOPHY */}
      <section className="px-6 md:px-12 py-24 bg-slate-950">
        <div className="max-w-7xl mx-auto">
          <h2 className="text-5xl md:text-6xl font-black mb-4 text-center">
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">The Zero Philosophy</span>
          </h2>
          <p className="text-center text-gray-400 text-lg mb-16">Not branding decoration. Real operating doctrine.</p>

          <div className="grid md:grid-cols-2 gap-8">
            {zeroPillars.map((pillar, idx) => (
              <div key={idx} className="bg-gradient-to-br from-slate-800 to-slate-900 rounded-lg p-8 border border-slate-700 hover:border-blue-500/50 transition hover:shadow-lg hover:shadow-blue-500/20">
                <div className="flex items-center gap-4 mb-4">
                  <div className="text-blue-400">{pillar.icon}</div>
                  <h3 className="text-xl font-bold">{pillar.title}</h3>
                </div>
                <div className="space-y-4 text-sm">
                  <div>
                    <p className="text-gray-500 font-semibold mb-1">THE PROBLEM</p>
                    <p className="text-gray-300">{pillar.problem}</p>
                  </div>
                  <div>
                    <p className="text-gray-500 font-semibold mb-1">TITAN SOLUTION</p>
                    <p className="text-gray-300">{pillar.solution}</p>
                  </div>
                  <div className="pt-2 border-t border-slate-700">
                    <p className="text-blue-300 font-semibold italic">{pillar.outcome}</p>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* VERTICAL NICHE AI */}
      <section className="px-6 md:px-12 py-24 bg-black">
        <div className="max-w-7xl mx-auto">
          <h2 className="text-5xl md:text-6xl font-black mb-16 text-center">
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-400">Super Niche AI</span>
            <br />
            <span className="text-white">for Cleaning Operators</span>
          </h2>

          {/* Vertical Selector */}
          <div className="flex gap-4 justify-center mb-12 flex-wrap">
            {Object.entries(verticals).map(([key, data]) => (
              <button
                key={key}
                onClick={() => setSelectedVertical(key)}
                className={`px-6 py-3 rounded-lg font-semibold transition ${
                  selectedVertical === key
                    ? 'bg-blue-600 text-white'
                    : 'bg-slate-800 text-gray-300 hover:bg-slate-700'
                }`}
              >
                {data.name}
              </button>
            ))}
          </div>

          {/* Vertical Details */}
          <div className="bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl p-12 border border-slate-700">
            <h3 className="text-3xl font-bold mb-8 text-cyan-300">{verticals[selectedVertical].name}</h3>

            <div className="grid md:grid-cols-3 gap-8">
              <div>
                <h4 className="text-sm font-bold text-gray-400 mb-4 uppercase tracking-wide">Niche Workflows</h4>
                <ul className="space-y-3">
                  {verticals[selectedVertical].workflows.map((item, idx) => (
                    <li key={idx} className="flex items-start gap-3">
                      <CheckCircle className="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" />
                      <span className="text-gray-200">{item}</span>
                    </li>
                  ))}
                </ul>
              </div>

              <div>
                <h4 className="text-sm font-bold text-gray-400 mb-4 uppercase tracking-wide">Compliance Logic</h4>
                <ul className="space-y-3">
                  {verticals[selectedVertical].compliance.map((item, idx) => (
                    <li key={idx} className="flex items-start gap-3">
                      <Shield className="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" />
                      <span className="text-gray-200">{item}</span>
                    </li>
                  ))}
                </ul>
              </div>

              <div>
                <h4 className="text-sm font-bold text-gray-400 mb-4 uppercase tracking-wide">Auto-Generated Artifacts</h4>
                <ul className="space-y-3">
                  {verticals[selectedVertical].artifacts.map((item, idx) => (
                    <li key={idx} className="flex items-start gap-3">
                      <BookOpen className="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" />
                      <span className="text-gray-200">{item}</span>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* NINE APPS */}
      <section className="px-6 md:px-12 py-24 bg-slate-950">
        <div className="max-w-7xl mx-auto">
          <h2 className="text-5xl md:text-6xl font-black mb-4 text-center">
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-400">Up to Nine Apps</span>
          </h2>
          <p className="text-center text-gray-400 text-lg mb-16">One cleaning business operating system.</p>

          <div className="grid md:grid-cols-3 gap-6">
            {apps.map((app, idx) => (
              <div
                key={idx}
                className="bg-gradient-to-br from-slate-800 to-slate-900 rounded-lg p-6 border border-slate-700 hover:border-blue-500/50 transition cursor-pointer group"
                onClick={() => setExpandedSection(expandedSection === idx ? null : idx)}
              >
                <div className="text-4xl mb-3">{app.icon}</div>
                <p className="text-xs font-bold text-blue-400 mb-1 uppercase tracking-wider">{app.tier}</p>
                <h3 className="text-2xl font-bold mb-1 group-hover:text-blue-300 transition">{app.name}</h3>
                <p className="text-xs text-cyan-300 font-semibold mb-3 italic">{app.category}</p>
                <p className="text-sm text-gray-400 mb-4">{app.tagline}</p>
                
                <div className="text-xs bg-slate-700/50 rounded px-3 py-2 mb-4 text-gray-300">
                  <span className="font-bold text-blue-300">Owns: </span>{app.owns}
                </div>

                {expandedSection === idx && (
                  <div className="mt-6 pt-6 border-t border-slate-700 space-y-4">
                    <div>
                      <p className="text-xs font-bold text-gray-500 mb-2">CONTROL PLANE</p>
                      <ul className="space-y-2">
                        {app.features.map((f, i) => (
                          <li key={i} className="text-sm text-gray-300 flex items-start gap-2">
                            <span className="w-1.5 h-1.5 bg-blue-400 rounded-full mt-1.5 flex-shrink-0"></span> <span>{f}</span>
                          </li>
                        ))}
                      </ul>
                    </div>
                    <div className="pt-4 border-t border-slate-700">
                      <p className="text-sm font-semibold text-cyan-300">→ {app.impact}</p>
                    </div>
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* OPERATING PRINCIPLE */}
      <section className="px-6 md:px-12 py-24 bg-black">
        <div className="max-w-5xl mx-auto text-center">
          <h2 className="text-5xl md:text-6xl font-black mb-12">
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-500">
              Hello Receives. Zero Thinks. Titan Executes.
            </span>
          </h2>
          <div className="space-y-6 text-lg text-gray-300">
            <p><span className="text-blue-400 font-bold">Hello Receives:</span> Customer books a job. Titan Hello sends confirmation and arrival notice.</p>
            <p><span className="text-cyan-400 font-bold">Zero Thinks:</span> AI analyzes job and auto-generates checklist, SWMS, training flow, or artifact as needed.</p>
            <p><span className="text-blue-400 font-bold">Titan Executes:</span> Go loads the job. Technician executes. Ground Zero tracks progress. Pro inspects quality. ZeroPay collects payment.</p>
          </div>
          <p className="mt-12 text-gray-500 italic">Everything connects. One business. One system. No duplication.</p>
        </div>
      </section>

      {/* FINAL CTA */}
      <section className="px-6 md:px-12 py-24 bg-gradient-to-r from-blue-900/20 to-cyan-900/20 border-t border-slate-700">
        <div className="max-w-4xl mx-auto text-center">
          <h2 className="text-4xl font-black mb-8">Ready to remove the BS from your cleaning operation?</h2>
          <button className="px-10 py-5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-lg transition shadow-lg shadow-blue-600/50">
            Launch Titan Zero — Start Free Trial
          </button>
          <p className="text-gray-400 mt-6 text-sm">One vertical. One app. Zero BS.</p>
        </div>
      </section>

      {/* FOOTER */}
      <footer className="px-6 md:px-12 py-12 bg-black border-t border-slate-800">
        <div className="max-w-7xl mx-auto text-center text-gray-500 text-sm">
          <p>Titan BOS — The operating system for cleaning businesses. Titan Capability. Zero BS.</p>
        </div>
      </footer>
    </div>
  );
}
