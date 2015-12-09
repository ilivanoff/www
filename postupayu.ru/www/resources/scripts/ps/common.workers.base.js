var PsWorkersHelper = {
    TYPE: {
        LOG:     0,
        SUCCESS: 1,
        ERROR:   2
    },
    
    /**
     * Логгер для воркера
     */
    getLogger: function(workerName, taskNumWorker, isClient) {
        return PsLogger.inst('Worker['+workerName+'#'+PsStrings.padLeft(taskNumWorker, 0, 3) +']'+(isClient?'-CLT' : '-SRV')).setTrace();
    },

    srvMsgLog: function(logEvent) {
        return {
            type: PsWorkersHelper.TYPE.LOG,
            msg: logEvent
        }
    },
    
    srvMsgOnSuccess: function(msg) {
        return {
            type: PsWorkersHelper.TYPE.SUCCESS,
            msg: msg
        }
    },

    srvMsgOnError: function(error) {
        return {
            type: PsWorkersHelper.TYPE.ERROR,
            msg:  PsUtil.extractErrMsg(error)
        }
    }
}