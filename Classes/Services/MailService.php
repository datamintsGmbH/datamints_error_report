<?php

namespace Datamints\DatamintsErrorReport\Services;


use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***
 *
 * This file is part of the "datamints error_report" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2022 Mark Weisgerber <m.weisgerber@datamints.com>, datamints GmbH
 *
 ***/

/**
 * MailService
 */
class MailService extends AbstractService
{

    /**
     * Provide template for the mail as a standalone fluid template
     *
     * @param string $templateFilename
     * @param array  $variables
     *
     * @return string
     */
    public function getRenderedReport ($templateFilename = '', $variables = [])
    {
        $templatePath = 'Backend/Reports/' . $templateFilename;
        $standaloneView = \Datamints\DatamintsErrorReport\Utility\ErrorReportUtility::getFluidStandaloneViewWithTemplate($templatePath);
        $standaloneView->assignMultiple($variables);

        return $standaloneView->render();
    }

    /**
     * Sends the report email
     *
     * @param string $receiver    Receiver
     * @param string $subjectText Subject
     * @param string $bodyText    Mailtext
     * @param array  $attachements
     */
    public function sendMailByString (string $receiver, string $subjectText, string $bodyText, array $attachements = []): void
    {
        $defaultFromSender = \TYPO3\CMS\Core\Utility\MailUtility::getSystemFrom();

        /** @var \TYPO3\CMS\Core\Mail\MailMessage $mail */
        $mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(MailMessage::class);
        $mail->setSubject($subjectText);
        if (GeneralUtility::validEmail(\TYPO3\CMS\Core\Utility\MailUtility::getSystemFromAddress())) {
            $mail->setFrom($defaultFromSender);
        } else {
            // Placeholder if nothing is entered in the install tool
            $mail->setFrom(['info@datamints.com' => 'please define mail sender in installtool']);
        }
        // this header tells auto-repliers ("email holiday mode") to not
        // reply to this message because it's an automated email
        $mail->getHeaders()->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply');
        // Wir markieren die Mail als sehr wichtig
        $mail->priority(MailMessage::PRIORITY_HIGHEST);


        $mail->setTo([$receiver => $receiver]);
        if (count($attachements) > 0) {
            foreach ($attachements as $attachement) {
                $mail->attach(
                    $attachement['data'],
                    $attachement['filename'],
                    $attachement['contentType']
                );
            }

        }
        $mail->html($bodyText);
        $mail->send();
    }

}
