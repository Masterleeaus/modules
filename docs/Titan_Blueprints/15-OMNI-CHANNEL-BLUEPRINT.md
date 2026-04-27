# Omni Channel Blueprint

Status: Canonical draft  
Layer: Omnichannel interaction shell

## Role

Omni is the conversational, omnichannel operating shell across WhatsApp, Telegram, Messenger, email, and future voice/chat interfaces. It sits above communications and below AI orchestration.

## Tree

```text
app/Platform/Omni/
├─ Threads/
├─ Conversations/
├─ ChannelSessions/
├─ Routing/
├─ Inbox/
├─ Campaigns/
├─ AutoResponses/
├─ LeadRouting/
├─ Escalations/
├─ Summaries/
├─ Memory/
└─ Support/
```

## Core Responsibilities

### Conversation Layer
- thread history
- participant mapping
- context summaries
- channel identity linking

### Routing Layer
- choose assistant / queue / user
- map inbound intent to module or AI tool
- escalate from automation to human review

### Inbox Layer
- unified inbox view
- message state
- assignment and triage
- unresolved thread aging

### Campaign Layer
- outbound sequences
- channel-specific publishing
- lead follow-up ladders
- pause / resume / opt-out control

## Relationship to Other Layers

- Communications handles message delivery.
- Omni handles the multi-channel operating model.
- AI handles reasoning, drafting, routing recommendations, and safe action proposals.

## Channel Targets

Preferred first-class channels:
- Email
- WhatsApp
- Telegram
- Messenger
- SMS
- Voice later through Titan Talk / voice layer
