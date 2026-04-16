# n8n AI Automation Setup Guide

The PHP application invokes an n8n webhook (`http://localhost:5678/webhook/new-grievance`) every time a new grievance is submitted. 

## Requirements
- Node.js installed on your machine.
- Local n8n instance.

## Starting n8n
To start n8n locally without a full Docker install, you can simply run:
```bash
npx n8n
```

## Creating the Workflow
1. Once n8n is running, open `http://localhost:5678` in your browser.
2. Create a new Workflow.
3. Add a **Webhook** node:
   - Set the HTTP method to **POST**.
   - Set the Path to `new-grievance`.
   - Ensure the Webhook URL matches: `http://localhost:5678/webhook/new-grievance` (You may need to toggle the node to active to listen to production requests, or use the dev URL during testing).
4. Connect the Webhook to an **AI Node** (e.g., OpenAI or generic LLM):
   - Configure the prompt to: "Summarize this student issue briefly and determine if the priority needs to be escalated from its current level: {{ $json.body.description }}"
5. Add a **MySQL** node to update the `grievances` table:
   - Operation: **Execute Query**
   - Query: `UPDATE grievances SET ai_summary = '{{ $json.message.content }}' WHERE id = '{{ $json.body.id }}'`
6. Save and activate the workflow.

When the student submits the form on the dashboard, n8n will catch the webhook, query the AI, and inject the smart summary back into the Database.
