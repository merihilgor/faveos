<?php

namespace App\Plugins\Ldap\Adldap\Models;

class Printer extends Entry
{
    /**
     * Returns the printers name.
     *
     * https://msdn.microsoft.com/en-us/library/ms679385(v=vs.85).aspx
     *
     * @return string
     */
    public function getPrinterName()
    {
        return $this->getAttribute($this->schema->printerName(), 0);
    }

    /**
     * Returns the printers share name.
     *
     * https://msdn.microsoft.com/en-us/library/ms679408(v=vs.85).aspx
     *
     * @return string
     */
    public function getPrinterShareName()
    {
        return $this->getAttribute($this->schema->printerShareName(), 0);
    }

    /**
     * Returns the printers memory.
     *
     * https://msdn.microsoft.com/en-us/library/ms679396(v=vs.85).aspx
     *
     * @return string
     */
    public function getMemory()
    {
        return $this->getAttribute($this->schema->printerMemory(), 0);
    }

    /**
     * Returns the printers URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->getAttribute($this->schema->url(), 0);
    }

    /**
     * Returns the printers location.
     *
     * https://msdn.microsoft.com/en-us/library/ms676839(v=vs.85).aspx
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->getAttribute($this->schema->location(), 0);
    }

    /**
     * Returns the server name that the
     * current printer is connected to.
     *
     * https://msdn.microsoft.com/en-us/library/ms679772(v=vs.85).aspx
     *
     * @return string
     */
    public function getServerName()
    {
        return $this->getAttribute($this->schema->serverName(), 0);
    }

    /**
     * Returns true / false if the printer can print in color.
     *
     * https://msdn.microsoft.com/en-us/library/ms679382(v=vs.85).aspx
     *
     * @return null|bool
     */
    public function getColorSupported()
    {
        return $this->convertStringToBool($this->getAttribute($this->schema->printerColorSupported(), 0));
    }

    /**
     * Returns true / false if the printer supports duplex printing.
     *
     * https://msdn.microsoft.com/en-us/library/ms679383(v=vs.85).aspx
     *
     * @return null|bool
     */
    public function getDuplexSupported()
    {
        return $this->convertStringToBool($this->getAttribute($this->schema->printerDuplexSupported(), 0));
    }

    /**
     * Returns an array of printer paper types that the printer supports.
     *
     * https://msdn.microsoft.com/en-us/library/ms679395(v=vs.85).aspx
     *
     * @return array
     */
    public function getMediaSupported()
    {
        return $this->getAttribute($this->schema->printerMediaSupported());
    }

    /**
     * Returns true / false if the printer supports stapling.
     *
     * https://msdn.microsoft.com/en-us/library/ms679410(v=vs.85).aspx
     *
     * @return null|bool
     */
    public function getStaplingSupported()
    {
        return $this->convertStringToBool($this->getAttribute($this->schema->printerStaplingSupported(), 0));
    }

    /**
     * Returns an array of the printers bin names.
     *
     * https://msdn.microsoft.com/en-us/library/ms679380(v=vs.85).aspx
     *
     * @return array
     */
    public function getPrintBinNames()
    {
        return $this->getAttribute($this->schema->printerBinNames());
    }

    /**
     * Returns the printers maximum resolution.
     *
     * https://msdn.microsoft.com/en-us/library/ms679391(v=vs.85).aspx
     *
     * @return string
     */
    public function getPrintMaxResolution()
    {
        return $this->getAttribute($this->schema->printerMaxResolutionSupported(), 0);
    }

    /**
     * Returns the printers orientations supported.
     *
     * https://msdn.microsoft.com/en-us/library/ms679402(v=vs.85).aspx
     *
     * @return string
     */
    public function getPrintOrientations()
    {
        return $this->getAttribute($this->schema->printerOrientationSupported(), 0);
    }

    /**
     * Returns the driver name of the printer.
     *
     * https://msdn.microsoft.com/en-us/library/ms675652(v=vs.85).aspx
     *
     * @return string
     */
    public function getDriverName()
    {
        return $this->getAttribute($this->schema->driverName(), 0);
    }

    /**
     * Returns the printer drivers version number.
     *
     * https://msdn.microsoft.com/en-us/library/ms675653(v=vs.85).aspx
     *
     * @return string
     */
    public function getDriverVersion()
    {
        return $this->getAttribute($this->schema->driverVersion(), 0);
    }

    /**
     * Returns the priority number of the printer.
     *
     * https://msdn.microsoft.com/en-us/library/ms679413(v=vs.85).aspx
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->getAttribute($this->schema->priority(), 0);
    }

    /**
     * Returns the printers start time.
     *
     * https://msdn.microsoft.com/en-us/library/ms679411(v=vs.85).aspx
     *
     * @return string
     */
    public function getPrintStartTime()
    {
        return $this->getAttribute($this->schema->printerStartTime(), 0);
    }

    /**
     * Returns the printers end time.
     *
     * https://msdn.microsoft.com/en-us/library/ms679384(v=vs.85).aspx
     *
     * @return string
     */
    public function getPrintEndTime()
    {
        return $this->getAttribute($this->schema->printerEndTime(), 0);
    }

    /**
     * Returns the port name of printer.
     *
     * https://msdn.microsoft.com/en-us/library/ms679131(v=vs.85).aspx
     *
     * @return string
     */
    public function getPortName()
    {
        return $this->getAttribute($this->schema->portName(), 0);
    }

    /**
     * Returns the printers version number.
     *
     * https://msdn.microsoft.com/en-us/library/ms680897(v=vs.85).aspx
     *
     * @return string
     */
    public function getVersionNumber()
    {
        return $this->getAttribute($this->schema->versionNumber(), 0);
    }

    /**
     * Returns the print rate.
     *
     * https://msdn.microsoft.com/en-us/library/ms679405(v=vs.85).aspx
     *
     * @return string
     */
    public function getPrintRate()
    {
        return $this->getAttribute($this->schema->printerPrintRate(), 0);
    }

    /**
     * Returns the print rate unit.
     *
     * https://msdn.microsoft.com/en-us/library/ms679406(v=vs.85).aspx
     *
     * @return string
     */
    public function getPrintRateUnit()
    {
        return $this->getAttribute($this->schema->printerPrintRate(), 0);
    }
}
