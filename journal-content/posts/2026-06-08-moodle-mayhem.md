---
title: Moodle Mayhem
date: 2026-06-08
summary: Notes from joining Action Sustainability, prototyping Moodle from scratch, and why we're extending it with our own plugins rather than fighting the core.
---

Upon joining my current role at [Action Sustainability](https://www.actionsustainability.com/) I was confronted with the prospect of [Moodle](https://moodle.org/) (not to be confused with noodle 🍜 as my autocorrect kept telling me).

I'd never touched Moodle before joining — my LMS journeys had been in custom platforms or CMSes that have been twisted to behave like an LMS.

It's been a great journey and so I'd thought, mainly for my own sanity, to note down some of the things that I found out.

## 1. Everyone has an opinion

"I love Moodle, I hate Moodle, Blackboard is so much better, it can do anything, it can't do anything, it's been going for over 20 years, I can't believe it's still here for over 20 years."

## 2. It's not a ceiling to our growth

My default approach to most things is "what is the problem we're trying to solve"? Would our current LMS hinder our growth and our member's personal growth through learning.

The answer is no.

## 3. But it's not easy

Our Moodle platform is 5 years old and heavily customised. My initial reaction was to ditch the customisations, follow Moodle's out-of-the-box approach so we get a safe upgrade path and off we go.

In order to put my mind at ease, I downloaded Moodle, and with a bit of help from Claude, spent 3 weeks vibe coding a prototype, sticking strictly to out-of-the-box functionality and seeing how far I could get in order to match our approach to learning.

I learnt an awful lot! Why I still love coding, AWS EC2 setup, Moodle's outdated theming (it has traces of [YUI](https://github.com/yui/yui3) which is a blast from the past for me), the double hop (more on that later), and its solid tracking capabilities. This leads to….

## 4. Our business model is not Moodle's business model

Nice idea Mike, but no - my prototype quickly showed the fork in the road. Moodle has its own perspective on how learners should learn and it does not strictly align to our successful approach (more about that another time!).

## 5. It's a piece of infrastructure, not a SaaS platform

I pulled back on my approach and had some lovely conversations with our product squad. We all agreed that Moodle, at its core, will function perfectly; but we'd need to "twist" it to make an incredible product for our users. We will do this by extending it by creating our own plugins ([Moodle Plugins directory](https://moodle.org/plugins/)).

This is our chosen path which allows us to imprint our approach to our learning experience - we've got loads of great ideas and in 12 months I think our users will really feel the difference.