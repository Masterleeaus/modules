<?php

return json_decode(<<<'JSON'
{
  "tiers": {
    "tier-1": "Tier 1 \u2014 Core Cleaning Verticals",
    "tier-2": "Tier 2 \u2014 Specialist High-Margin Verticals",
    "tier-3": "Tier 3 \u2014 Property & Exterior Service Verticals",
    "tier-4": "Tier 4 \u2014 Mobile Specialty Service Verticals"
  },
  "verticals": [
    {
      "tier": "tier-1",
      "name": "Residential Cleaning",
      "slug": "residential-cleaning",
      "description": "Recurring domestic services.",
      "includes": "weekly cleaning, fortnightly cleaning, deep cleans, spring cleans",
      "requirements": [
        "recurring scheduling",
        "route clustering",
        "client preferences",
        "supply tracking",
        "solo-operator optimization"
      ],
      "dashboards": [
        "Titan Solo",
        "Titan Go",
        "Ground Zero",
        "ZeroPay"
      ]
    },
    {
      "tier": "tier-1",
      "name": "Commercial & Office Cleaning",
      "slug": "commercial-office-cleaning",
      "description": "Contract-based facility maintenance.",
      "includes": "office cleaning, retail cleaning, schools, clinics (non-sterile environments)",
      "requirements": [
        "after-hours lock-up checklists",
        "attendance verification",
        "supply monitoring",
        "digital logbooks",
        "inspection scoring"
      ],
      "dashboards": [
        "Ground Zero",
        "Titan Pro",
        "Titan Go",
        "Zero Fuss"
      ]
    },
    {
      "tier": "tier-1",
      "name": "Bond Cleaning (End-of-Lease)",
      "slug": "bond-cleaning",
      "description": "Inspection-grade cleaning for tenancy turnover.",
      "includes": "agent-approved end-of-lease cleaning",
      "requirements": [
        "agent-approved checklists",
        "bond pack generator",
        "re-clean workflow",
        "carpet receipt tracking",
        "key custody tracking"
      ],
      "dashboards": [
        "Titan Go",
        "Titan Zero",
        "Zero Fuss",
        "ZeroPay"
      ]
    },
    {
      "tier": "tier-1",
      "name": "Airbnb / Short-Stay Turnover Cleaning",
      "slug": "airbnb-short-stay-turnover-cleaning",
      "description": "High-velocity reset cleaning synchronized with booking calendars.",
      "includes": "short-stay resets and turnover cleaning",
      "requirements": [
        "PMS integration",
        "auto-scheduling from checkout",
        "staging photo verification",
        "linen logistics",
        "damage reporting"
      ],
      "dashboards": [
        "Ground Zero",
        "Titan Go",
        "Titan Zero",
        "Titan Hello"
      ]
    },
    {
      "tier": "tier-1",
      "name": "Construction Site Cleaning",
      "slug": "construction-site-cleaning",
      "description": "Stage-based builder cleans.",
      "includes": "builders cleans and construction handovers",
      "requirements": [
        "zone-based tracking",
        "trade coordination",
        "SWMS / JSA compliance",
        "back-charge documentation",
        "delivery alignment scheduling"
      ],
      "dashboards": [
        "Ground Zero",
        "Titan Go",
        "Titan Pro",
        "Titan Zero"
      ]
    },
    {
      "tier": "tier-2",
      "name": "Biohazard & Crime Scene Cleaning",
      "slug": "biohazard-crime-scene-cleaning",
      "description": "Trauma and contamination response services.",
      "includes": "specialist contamination response",
      "requirements": [
        "chain-of-custody logging",
        "PPE enforcement",
        "hazard classification",
        "waste disposal certification",
        "incident documentation packs"
      ],
      "dashboards": [
        "Titan Go",
        "Titan Zero",
        "Ground Zero"
      ]
    },
    {
      "tier": "tier-2",
      "name": "Medical Equipment Lifecycle Cleaning",
      "slug": "medical-equipment-lifecycle-cleaning",
      "description": "Compliance-driven equipment sanitation tracking.",
      "includes": "equipment sanitation and lifecycle readiness",
      "requirements": [
        "traceability",
        "cycle logging",
        "audit-ready documentation",
        "ATP test recording",
        "maintenance readiness verification"
      ],
      "dashboards": [
        "Titan Zero",
        "Titan Pro",
        "Ground Zero"
      ]
    },
    {
      "tier": "tier-2",
      "name": "Solar Panel Cleaning (Industrial)",
      "slug": "solar-panel-cleaning-industrial",
      "description": "Utility-scale and rooftop solar maintenance.",
      "includes": "solar maintenance and efficiency protection",
      "requirements": [
        "weather-gated scheduling",
        "efficiency tracking",
        "height certification tracking",
        "panel-zone routing"
      ],
      "dashboards": [
        "Ground Zero",
        "Titan Go",
        "Titan Pro"
      ]
    },
    {
      "tier": "tier-2",
      "name": "Industrial Window Cleaning",
      "slug": "industrial-window-cleaning",
      "description": "High-rise facade cleaning services.",
      "includes": "height access facade cleaning",
      "requirements": [
        "height safety compliance",
        "equipment tracking",
        "weather gating",
        "permit logging"
      ],
      "dashboards": [
        "Ground Zero",
        "Titan Go",
        "Titan Zero"
      ]
    },
    {
      "tier": "tier-3",
      "name": "Pool Maintenance",
      "slug": "pool-maintenance",
      "description": "Water chemistry and filtration service workflows.",
      "includes": "pool service and water chemistry workflows",
      "requirements": [
        "chemical logging",
        "compliance tracking",
        "service interval automation",
        "equipment lifecycle tracking"
      ],
      "dashboards": [
        "Titan Go",
        "Titan Solo",
        "Titan Zero"
      ]
    },
    {
      "tier": "tier-3",
      "name": "Garden & Grounds Maintenance",
      "slug": "garden-grounds-maintenance",
      "description": "Outdoor recurring service workflows.",
      "includes": "grounds care and recurring outdoor maintenance",
      "requirements": [
        "seasonal scheduling logic",
        "route clustering",
        "equipment tracking",
        "weather gating"
      ],
      "dashboards": [
        "Titan Go",
        "Ground Zero",
        "Titan Solo"
      ]
    },
    {
      "tier": "tier-3",
      "name": "Property Manager Partner Platform",
      "slug": "property-manager-partner-platform",
      "description": "Real estate agency integration overlay.",
      "includes": "agency-connected property service workflows",
      "requirements": [
        "direct booking links",
        "audit trail preservation",
        "key custody tracking",
        "agent notification automation",
        "bond pack verification bundles"
      ],
      "dashboards": [
        "Zero Fuss",
        "Titan Hello",
        "Titan Zero",
        "ZeroPay"
      ]
    },
    {
      "tier": "tier-3",
      "name": "Pressure Cleaning (Exterior Surface Cleaning)",
      "slug": "pressure-cleaning",
      "description": "High-demand residential and commercial exterior cleaning.",
      "includes": "driveways, paths, roofs, walls, commercial forecourts, car parks",
      "requirements": [
        "surface-type presets",
        "chemical selection logging",
        "water usage estimation",
        "before/after evidence capture",
        "equipment tracking"
      ],
      "dashboards": [
        "Titan Go",
        "Titan Solo",
        "Titan Pro"
      ]
    },
    {
      "tier": "tier-4",
      "name": "Car Detailing",
      "slug": "car-detailing",
      "description": "Mobile or fixed-location vehicle detailing operations.",
      "includes": "interior detailing, exterior wash, paint correction, ceramic coating, fleet detailing",
      "requirements": [
        "service package templates",
        "vehicle condition reports",
        "photo inspection workflows",
        "add-on upsell tracking",
        "consumables usage tracking"
      ],
      "dashboards": [
        "Titan Go",
        "Titan Solo",
        "ZeroPay",
        "Titan Studio"
      ]
    },
    {
      "tier": "tier-4",
      "name": "Pet Washing & Grooming (Mobile Services)",
      "slug": "pet-washing-grooming",
      "description": "Mobile pet hygiene services.",
      "includes": "dog washing, coat brushing, flea treatments, mobile grooming",
      "requirements": [
        "pet profile tracking",
        "coat condition notes",
        "treatment reminders",
        "repeat visit automation",
        "owner communication workflows"
      ],
      "dashboards": [
        "Titan Go",
        "Titan Solo",
        "Titan Hello"
      ]
    },
    {
      "tier": "tier-4",
      "name": "Oven / Fridge / Appliance Deep Cleaning",
      "slug": "appliance-deep-cleaning",
      "description": "High-margin domestic specialist services.",
      "includes": "oven detailing, rangehood cleaning, fridge sanitation, dishwasher restoration",
      "requirements": [
        "appliance-specific checklists",
        "chemical workflow tracking",
        "before/after verification",
        "add-on bundling logic"
      ],
      "dashboards": [
        "Titan Go",
        "Titan Solo",
        "ZeroPay"
      ]
    }
  ]
}
JSON, true);
