<?php
/**
 * Created by PhpStorm.
 * User: adamyu
 * Date: 2018/10/8
 * Time: 9:54
 */

namespace adamyxt\coin;

use yii\base\Component;

abstract class Coin extends Component
{
    /**
     * @event DepositEvent
     */
    const EVENT_BEFORE_DEPOSIT = 'beforeDeposit';
    /**
     * @event DepositEvent
     */
    const EVENT_AFTER_DEPOSIT = 'afterDeposit';
    /**
     * @event WithdrawEvent
     */
    const EVENT_BEFORE_WITHDRAW = 'beforeWithdraw';
    /**
     * @event WithdrawEvent
     */
    const EVENT_AFTER_WITHDRAW = 'afterWithdraw';
    /**
     * @event ExecEvent
     */
    const EVENT_AFTER_ERROR = 'afterError';

    protected $_client;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->open();
    }

    /**
     * 创建一个钱包客户端
     * @return mixed
     */
    public abstract function open(): void;

    /**
     * 同步区块信息(充值交易订单)
     * @return int
     */
    public function deposit(): string
    {
        $this->trigger(self::EVENT_BEFORE_DEPOSIT);
        $message_id = $this->block();
        $this->trigger(self::EVENT_AFTER_DEPOSIT);
        return $message_id;
    }

    /**
     * @return int
     */
    public abstract function block(): string;

    /**
     * @param UserWithdraw $user_withdraw
     * @return int
     */
    public function withdraw(UserWithdraw $user_withdraw): string
    {
        $event = new WithdrawEvent([
            'user_withdraw' => $user_withdraw,
        ]);

        $this->trigger(self::EVENT_BEFORE_WITHDRAW, $event);
        if ($event->handled) {
            return null;
        }

        $event->id = $this->push($event->user_withdraw);
        $this->trigger(self::EVENT_AFTER_WITHDRAW, $event);

        return $event->id;
    }

    /**
     * @param UserWithdraw $user_withdraw
     * @return int
     */
    public abstract function push(UserWithdraw $user_withdraw): string;

}