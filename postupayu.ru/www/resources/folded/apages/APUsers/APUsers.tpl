{foreach $users as $user}
    {$userId=$user->getId()}
    {$newMsgsCnt=FeedbackManager::inst()->getNotConfirmemMsgsCnt($userId)}
    <div class="user-box">
        <h3>Пользователь №{$user@index+1}. id:{$userId}, mail: {$user->getEmail()} {if $newMsgsCnt}<span class="new-msgs">Новых сообщений: {$newMsgsCnt}</span>{/if}</h3>
        {$user->getIdCard()}
        {psctrl id=$userId class='user_card_control' history='История обратной связи'}
    </div>
{/foreach}