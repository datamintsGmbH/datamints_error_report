.. include:: ../Includes.txt

.. _for-editors:

===========
For Editors
===========

- Open TYPO3 Backend and navigate to the Scheduler-Module
- Create a new Task
- Select Class Execute Console Command
- Then another option appears. Select: error-report:report:send: "..."
- Save to see our option-fields, see screenshot:

.. image:: Images/sample-scheduler.png
  :target: https://www.datamints.com/

.. _editor-faq:

FAQ
===

Q: How to test everything runs well?

A: The Extension provides also an task called: "error-report:dispatch: ...". Execute it to force a php-exception and then wait for the send-command to be executed or trigger it manually
