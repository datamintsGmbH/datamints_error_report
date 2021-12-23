<?php

namespace Datamints\DatamintsErrorReport\Services;


use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***
 *
 * This file is part of the "datamints elearning" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020 Mark Weisgerber <m.weisgerber@datamints.com>, datamints GmbH
 *
 ***/

/**
 * MailService
 */
class MailService extends AbstractService
{
    /**
     * Sendet eine Zusammenfassung einer Rechnung
     *
     * @param \Datamints\DatamintsElearning\Domain\Model\Bill $bill
     */
    public function sendBillSummary ($bill)
    {

    }

    /**
     * Template für die Mail bereitstellen
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
     * Schickt eine Mail
     *
     * @param string $receiver    Empfänger
     * @param string $subjectText Betreff
     * @param string $bodyText    Mailtext
     * @param array  $attachements
     */
    public function sendMailByString ($receiver, $subjectText, $bodyText, $attachements = [])
    {
        $defaultFromSender = \TYPO3\CMS\Core\Utility\MailUtility::getSystemFrom();

        /** @var \TYPO3\CMS\Core\Mail\MailMessage $mail */
        $mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(MailMessage::class);
        $mail->setSubject($subjectText);
        if (GeneralUtility::validEmail(\TYPO3\CMS\Core\Utility\MailUtility::getSystemFromAddress())) {
            $mail->setFrom($defaultFromSender);
        } else {
            $mail->setFrom(['info@datamints.com' => 'please define mail sender in installtool']); // Placeholder falls im Install Tool nichts eingetragen ist
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
