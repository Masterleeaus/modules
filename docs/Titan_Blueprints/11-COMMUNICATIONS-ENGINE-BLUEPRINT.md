# Communications Engine Blueprint

Status: Canonical draft  
Layer: Message composition, routing, and delivery

## Role

Communications is the unified outbound/inbound layer for email, SMS, WhatsApp, Telegram, Messenger, push, and voice. It should not live separately inside each module.

## Tree

```text
app/Platform/Communications/
├─ Mail/
│  ├─ Templates/
│  ├─ Mailables/
│  ├─ Builders/
│  └─ Transports/
├─ Notifications/
│  ├─ Channels/
│  ├─ Templates/
│  ├─ Formatters/
│  └─ Dispatch/
├─ Sms/
├─ WhatsApp/
├─ Telegram/
├─ Messenger/
├─ Email/
├─ Voice/
├─ Push/
├─ Routing/
├─ Preferences/
├─ Compliance/
└─ Support/
```

## Responsibilities

### Template System
- message templates by channel
- localization
- variable interpolation
- branded rendering

### Routing
- choose best channel
- honor user preferences
- fail over between channels
- choose urgency path

### Delivery
- queue-backed dispatch
- delivery receipts
- retries
- bounce / failure handling

### Inbound Handling
- normalize replies and webhook callbacks
- map replies to thread, lead, booking, invoice, or ticket
- trigger signal emission back into the system

## Standard Message Object

Recommended fields:
- `message_type`
- `channel`
- `recipient`
- `template_key`
- `payload`
- `company_id`
- `context_type`
- `context_id`
- `priority`
- `correlation_id`

## Module Relationship

Modules should emit intent such as:
- booking reminder required
- invoice chase required
- approval request required

The communications engine decides how to deliver it.

## Omni Relationship

Omni is the conversational and omnichannel operating shell. Communications is the message transport and formatting engine beneath Omni.
